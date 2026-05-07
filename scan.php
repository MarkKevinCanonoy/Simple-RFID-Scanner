<?php
require_once 'db.php';

$found_user  = null;
$not_found   = false;
$scanned_rfid = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfid_input = trim(mysqli_real_escape_string($conn, $_POST['rfid_scan'] ?? ''));
    $scanned_rfid = $rfid_input;

    if ($rfid_input !== '') {
        $res  = mysqli_query($conn, "SELECT full_name, student_id FROM users WHERE rfid_number = '$rfid_input' LIMIT 1");
        if (mysqli_num_rows($res) > 0) {
            $found_user = mysqli_fetch_assoc($res);
        } else {
            $not_found = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan RFID — RFID System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'navbar.php'; ?>

<div class="scan-wrapper">
    <div class="scan-card">

        <h1>Scan <span>RFID</span> Card</h1>

        <form method="POST" action="scan.php" id="scanForm" autocomplete="off">
            <div class="scan-input-wrap">
                <input
                    type="text"
                    id="rfid_scan"
                    name="rfid_scan"
                    class="scan-input"
                    placeholder="Waiting for card..."
                    autofocus
                >
            </div>
        </form>

        <div class="scan-result-box <?= $found_user ? 'success' : ($not_found ? 'error' : '') ?>" id="resultBox">
            <?php if ($found_user): ?>
                <div style="font-size:0.75rem;font-family:'Space Mono',monospace;color:var(--text-muted);letter-spacing:0.08em;margin-bottom:0.3rem;">STUDENT IDENTIFIED</div>
                <div class="result-name"><?= htmlspecialchars($found_user['full_name']) ?></div>
                <div class="result-student-id"><?= htmlspecialchars($found_user['student_id']) ?></div>
            <?php elseif ($not_found): ?>
                <div style="font-size:1.5rem;margin-bottom:0.4rem;">&#9888;</div>
                <div class="result-error-msg">User Not Found</div>
                <div style="font-family:'Space Mono',monospace;font-size:0.78rem;color:var(--text-muted);margin-top:0.3rem;">
                    RFID: <?= htmlspecialchars($scanned_rfid) ?>
                </div>
            <?php else: ?>
                <div class="result-placeholder">No card scanned yet.</div>
            <?php endif; ?>
        </div>

        <div class="scan-indicator">
            <div class="dot"></div>
            SCANNER READY
        </div>

    </div>
</div>

<script>
(function () {
    const input = document.getElementById('rfid_scan');
    const form  = document.getElementById('scanForm');

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (input.value.trim() !== '') {
                form.submit();
            }
        }
    });

    <?php if ($found_user || $not_found): ?>
    window.addEventListener('DOMContentLoaded', function () {
        input.value = '';
        input.focus();
    });
    <?php endif; ?>

    document.addEventListener('click', function (e) {
        if (e.target !== input) input.focus();
    });

    input.addEventListener('blur', function () {
        setTimeout(() => input.focus(), 50);
    });
})();
</script>

</body>
</html>