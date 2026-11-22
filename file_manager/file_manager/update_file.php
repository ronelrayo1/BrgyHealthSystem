<?php
require_once __DIR__ . '/fm_config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'replace') {
    $abs = fmResolve($_POST['file_path'] ?? '');
    if (!$abs || !is_file($abs)) {
        $error = 'File not found.';
    } elseif (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please choose a replacement file.';
    } else {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $abs) || copy($_FILES['file']['tmp_name'], $abs)) {
            $success = 'File updated successfully.';
        } else {
            $error = 'Failed to update file.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'rename') {
    $abs    = fmResolve($_POST['file_path'] ?? '');
    $newRel = str_replace('\\', '/', trim($_POST['new_name'] ?? ''));
    if (!$abs || !is_file($abs)) {
        $error = 'File not found.';
    } elseif ($newRel === '' || strpos($newRel, '..') !== false) {
        $error = 'Invalid new name.';
    } else {
        $newAbs = $FM_FILES_DIR . DIRECTORY_SEPARATOR . $newRel;
        $newDir = dirname($newAbs);
        if (!is_dir($newDir)) mkdir($newDir, 0755, true);
        if (file_exists($newAbs)) {
            $error = 'A file with that name already exists.';
        } elseif (rename($abs, $newAbs)) {
            $success = 'File renamed successfully.';
        } else {
            $error = 'Failed to rename file.';
        }
    }
}

$editPath = $_GET['file'] ?? '';
$editAbs  = fmResolve($editPath);
$editFile = ($editAbs && is_file($editAbs)) ? $editPath : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update File</title>
<link rel="stylesheet" href="../design/style.css">
</head>
<body class="fm-body">
<div class="box fm-page">
    <h1>Update File</h1>
    <?php if ($success): ?><div class="alert ok"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert err"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <?php if ($editFile): ?>
        <p>Editing: <strong><?php echo htmlspecialchars($editFile); ?></strong></p>

        <h2>Replace content</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="replace">
            <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($editFile); ?>">
            <label for="file">Choose new file</label>
            <input type="file" id="file" name="file" required>
            <button type="submit">Update File</button>
        </form>

        <h2>Rename</h2>
        <form method="POST">
            <input type="hidden" name="action" value="rename">
            <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($editFile); ?>">
            <label for="new_name">New name/path</label>
            <input type="text" id="new_name" name="new_name" value="<?php echo htmlspecialchars($editFile); ?>" required>
            <button type="submit">Rename</button>
        </form>
    <?php else: ?>
        <p>Select a file to update from the <a href="select_file.php">file list</a>.</p>
    <?php endif; ?>

    <p class="margin-top-15"><a href="select_file.php">&larr; Back to file list</a></p>
</div>
</body>
</html>
