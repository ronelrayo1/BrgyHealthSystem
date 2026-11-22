<?php

$FM_FILES_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'files';

if (!is_dir($FM_FILES_DIR)) {
    mkdir($FM_FILES_DIR, 0755, true);
}
$FM_FILES_DIR = realpath($FM_FILES_DIR);

function fmSanitize($name) {
    $name = basename($name);
    $name = preg_replace('/[^A-Za-z0-9._\- ]/', '_', $name);
    return trim($name) === '' ? 'file' : trim($name);
}

function fmResolve($rel) {
    global $FM_FILES_DIR;
    $rel = str_replace('\\', '/', trim($rel));
    if ($rel === '' || strpos($rel, '..') !== false) return null;
    $abs = realpath($FM_FILES_DIR . DIRECTORY_SEPARATOR . $rel);
    if ($abs === false) return null;
    if (strpos($abs, $FM_FILES_DIR) !== 0) return null;
    return $abs;
}

function fmList($dir = null, $base = '') {
    global $FM_FILES_DIR;
    if ($dir === null) $dir = $FM_FILES_DIR;
    $out = [];
    foreach ((array)@scandir($dir) as $item) {
        if ($item === '.' || $item === '..' || $item === '.htaccess') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $rel  = $base === '' ? $item : $base . '/' . $item;
        if (is_dir($path)) {
            $out = array_merge($out, fmList($path, $rel));
        } else {
            $out[] = [
                'name'     => $item,
                'path'     => $rel,
                'size'     => filesize($path),
                'modified' => filemtime($path),
            ];
        }
    }
    return $out;
}

function fmSize($b) {
    if ($b >= 1048576) return number_format($b / 1048576, 2) . ' MB';
    if ($b >= 1024)    return number_format($b / 1024, 2) . ' KB';
    return $b . ' B';
}
?>
