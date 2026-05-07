<?php

require_once 'db.php';

if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $del_id");
    header('Location: dashboard.php?deleted=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id   = (int)   $_POST['edit_id'];
    $full_name = trim(mysqli_real_escape_string($conn, $_POST['full_name'] ?? ''));
    $course    = trim(mysqli_real_escape_string($conn, $_POST['course'] ?? ''));

    $updates = [];
    
    if ($full_name !== '') {
        $updates[] = "full_name = '$full_name'";
    }
    if ($course !== '') {
        $updates[] = "course = '$course'";
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image_name);
        $updates[] = "image = '$image_name'";
    }

    if (!empty($updates)) {
        $set_clause = implode(', ', $updates);
        mysqli_query($conn, "UPDATE users SET $set_clause WHERE id = $edit_id");
    }

    header('Location: dashboard.php?updated=1');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
$users  = mysqli_fetch_all($result, MYSQLI_ASSOC);
$total  = count($users);

$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id   = (int) $_GET['edit'];
    $edit_res  = mysqli_query($conn, "SELECT * FROM users WHERE id = $edit_id LIMIT 1");
    $edit_user = mysqli_fetch_assoc($edit_res);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — RFID System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'navbar.php'; ?>

<div class="page-wrapper wide">

    <div class="page-header">
        <h1><span>Student</span> Dashboard</h1>
    </div>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">
            <span class="alert-icon">&#10003;</span>
            <div>Student registered successfully.</div>
        </div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-error">
            <span class="alert-icon">&#10005;</span>
            <div>Student record deleted.</div>
        </div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success">
            <span class="alert-icon">&#10003;</span>
            <div>Student record updated successfully.</div>
        </div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-chip">
            <div class="stat-label">Total Students</div>
            <div class="stat-value"><?= $total ?></div>
        </div>
        <div class="stat-chip">
            <div class="stat-label">RFID Cards Assigned</div>
            <div class="stat-value"><?= $total ?></div>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date Registered</th>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Full Name</th>
                    <th>Course</th>
                    <th>Student ID</th>
                    <th>RFID Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <span>&#9670;</span>
                                No students registered yet. <a href="index.php" style="color:var(--pink);">Register one now.</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td style="color:var(--text-muted);"><?= $i + 1 ?></td>
                            <td><?= date('M d, Y  H:i', strtotime($user['created_at'])) ?></td>
                            <td><span class="badge-id"><?= htmlspecialchars($user['id']) ?></span></td>
                            <td>
                                <?php if (!empty($user['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($user['image']) ?>" style="width: 36px; height: 36px; border-radius: 4px; object-fit: cover; border: 1px solid var(--border);">
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size: 0.75rem;">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="td-name"><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['course'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['student_id']) ?></td>
                            <td class="td-rfid"><?= htmlspecialchars($user['rfid_number']) ?></td>
                            <td class="td-actions">
                                <a href="dashboard.php?edit=<?= $user['id'] ?>" class="btn btn-edit">&#9998; Edit</a>
                                <a
                                    href="dashboard.php?delete=<?= $user['id'] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Delete <?= htmlspecialchars(addslashes($user['full_name'])) ?>? This cannot be undone.');"
                                >&#10005; Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php if ($edit_user): ?>
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Edit <span style="color:var(--pink);">Student Data</span></h2>
            <button class="modal-close" onclick="closeModal()">&#10005;</button>
        </div>

        <form method="POST" action="dashboard.php" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?= $edit_user['id'] ?>">
            
            <div class="form-group">
                <label class="form-label" for="edit_full_name">Full Name</label>
                <input
                    type="text"
                    id="edit_full_name"
                    name="full_name"
                    class="form-input"
                    value="<?= htmlspecialchars($edit_user['full_name']) ?>"
                    autofocus
                >
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label" for="edit_course">Course</label>
                <input
                    type="text"
                    id="edit_course"
                    name="course"
                    class="form-input"
                    placeholder="Leave blank to keep current"
                    value="<?= htmlspecialchars($edit_user['course'] ?? '') ?>"
                >
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label" for="edit_image">Update Image</label>
                <input
                    type="file"
                    id="edit_image"
                    name="image"
                    class="form-input"
                    accept="image/*"
                >
            </div>

            <div style="margin-top:0.8rem; color:var(--text-muted); font-size:0.78rem; font-family:'Space Mono',monospace;">
                Student ID: <?= htmlspecialchars($edit_user['student_id']) ?> &nbsp;|&nbsp;
                RFID: <?= htmlspecialchars($edit_user['rfid_number']) ?>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    history.replaceState(null, '', 'dashboard.php');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
<?php endif; ?>

</body>
</html>