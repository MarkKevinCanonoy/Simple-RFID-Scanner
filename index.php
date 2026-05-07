<?php

require_once 'db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name   = trim(mysqli_real_escape_string($conn, $_POST['full_name']   ?? ''));
    $student_id  = trim(mysqli_real_escape_string($conn, $_POST['student_id']  ?? ''));
    $rfid_number = trim(mysqli_real_escape_string($conn, $_POST['rfid_number'] ?? ''));

    if ($full_name === '' || $student_id === '' || $rfid_number === '') {
        $error = 'All fields are required. Please fill in every field.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE rfid_number = '$rfid_number' LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $error = "RFID <strong>{$rfid_number}</strong> is already registered to another student. Each card must be unique.";
        } else {
            $sql = "INSERT INTO users (full_name, student_id, rfid_number)
                    VALUES ('$full_name', '$student_id', '$rfid_number')";

            if (mysqli_query($conn, $sql)) {
                header('Location: dashboard.php?registered=1');
                exit;
            } else {
                $error = 'Database error: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student — RFID System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'navbar.php'; ?>

<div class="page-wrapper">

    <div class="page-header">
        <h1>Register <span>Student</span></h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-icon">&#9888;</span>
            <div><?= $error ?></div>
        </div>
    <?php endif; ?>

    <div class="card" style="max-width: 520px;">
        <form method="POST" action="index.php" autocomplete="off">
            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        class="form-input"
                        placeholder="e.g. Jose Rizal"
                        value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="student_id">Student ID</label>
                    <input
                        type="text"
                        id="student_id"
                        name="student_id"
                        class="form-input"
                        placeholder="e.g. 2024-00067"
                        value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="rfid_number">RFID Number</label>
                    <input
                        type="text"
                        id="rfid_number"
                        name="rfid_number"
                        class="form-input"
                        placeholder="type RFID code"
                        value="<?= htmlspecialchars($_POST['rfid_number'] ?? '') ?>"
                        required
                    >
                </div>

                <div style="margin-top: 0.5rem;">
                    <button type="submit" class="btn btn-primary btn-full">
                        &#43; Register Student
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>
</body>
</html>