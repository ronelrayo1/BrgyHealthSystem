<?php
require_once __DIR__ . '/fm_config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $abs = fmResolve($_POST['file_path'] ?? '');
    if (!$abs || !is_file($abs)) {
        $error = 'File not found.';
    } elseif (unlink($abs)) {
        $success = 'File deleted successfully.';
    } else {
        $error = 'Failed to delete file.';
    }
}

$files = fmList();
usort($files, fn($a, $b) => strcmp($a['path'], $b['path']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delete File</title>
<link rel="stylesheet" href="../design/style.css">
</head>
<body class="fm-body">
<div class="box fm-page">
    <h1>Delete File</h1>
    <?php if ($success): ?><div class="alert ok"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert err"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <?php if (empty($files)): ?>
        <p>No files to delete.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>File</th><th>Size</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($files as $f): ?>
            <tr>
                <td><?php echo htmlspecialchars($f['path']); ?></td>
                <td><?php echo fmSize($f['size']); ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete <?php echo htmlspecialchars($f['path'], ENT_QUOTES); ?>?');">
                        <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($f['path']); ?>">
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <p class="margin-top-15"><a href="select_file.php">&larr; Back to file list</a></p>
</div>
</body>
</html>
