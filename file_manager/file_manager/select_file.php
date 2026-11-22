<?php
require_once __DIR__ . '/fm_config.php';

$files = fmList();
usort($files, fn($a, $b) => strcmp($a['path'], $b['path']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select File</title>
<link rel="stylesheet" href="../design/style.css">
</head>
<body class="fm-body">
<div class="box fm-page">
    <h1>Select File</h1>
    <p class="sub">Folder: <code>file_manager/files/</code> &mdash; <?php echo count($files); ?> file(s)</p>

    <div class="nav">
        <a href="add_file.php">+ Add File</a>
        <a href="update_file.php">Update File</a>
        <a href="delete_file.php">Delete File</a>
    </div>

    <?php if (empty($files)): ?>
        <p>No files yet. <a href="add_file.php">Add one now</a>.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Name</th><th>Size</th><th>Modified</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($files as $f): ?>
            <tr>
                <td><a href="files/<?php echo htmlspecialchars($f['path']); ?>" target="_blank"><?php echo htmlspecialchars($f['path']); ?></a></td>
                <td><?php echo fmSize($f['size']); ?></td>
                <td><?php echo date('M d, Y H:i', $f['modified']); ?></td>
                <td>
                    <a href="update_file.php?file=<?php echo urlencode($f['path']); ?>">Update</a> |
                    <a href="delete_file.php" onclick="return confirm('Go to delete page?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
