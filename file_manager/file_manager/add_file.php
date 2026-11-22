<?php
require_once __DIR__ . '/fm_config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload error. Please try again.';
    } else {
        $name = fmSanitize($_FILES['file']['name']);
        $dest = $FM_FILES_DIR . DIRECTORY_SEPARATOR . $name;

        if (file_exists($dest)) {
            $info = pathinfo($name);
            $i = 1;
            do {
                $dest = $FM_FILES_DIR . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $i . ($info['extension'] ? '.' . $info['extension'] : '');
                $i++;
            } while (file_exists($dest));
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
            $success = 'File added successfully: ' . basename($dest);
        } else {
            $error = 'Failed to add file.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add File</title>
    <link rel="stylesheet" href="../design/style.css">
</head>
<body class="fm-body">
    <div class="box fm-page">
        <h1>Add File</h1>

        <?php if ($success): ?>
            <div class="alert ok"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert err"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="file">Choose file</label>
            <input type="file" id="file" name="file" required>
            <button type="submit">Add File</button>
        </form>

        <p class="margin-top-15">
            <a href="select_file.php">&larr; Back to file list</a>
        </p>
    </div>
</body>
</html>
