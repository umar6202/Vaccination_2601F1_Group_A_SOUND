<?php
/**
 * VEBRA Safe Media Upload Helper
 * Validates MIME types, extensions, file size, and generates unique safe filenames.
 * Strictly blocks executable files.
 */

function vebra_handle_upload(array $file, string $type, string $destSubdir): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameter.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file was uploaded.', 'no_file' => true];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    $configs = [
        'audio' => [
            'max_size'   => 50 * 1024 * 1024, // 50MB
            'extensions' => ['mp3', 'wav', 'ogg', 'm4a'],
            'mimes'      => ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/ogg', 'audio/x-m4a', 'audio/mp4', 'application/octet-stream'],
            'prefix'     => 'track_'
        ],
        'video' => [
            'max_size'   => 120 * 1024 * 1024, // 120MB
            'extensions' => ['mp4', 'webm', 'ogv'],
            'mimes'      => ['video/mp4', 'video/webm', 'video/ogg', 'application/octet-stream'],
            'prefix'     => 'vid_'
        ],
        'image' => [
            'max_size'   => 8 * 1024 * 1024, // 8MB
            'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'svg'],
            'mimes'      => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'],
            'prefix'     => 'img_'
        ]
    ];

    if (!isset($configs[$type])) {
        return ['success' => false, 'error' => 'Unsupported media type specification.'];
    }

    $cfg = $configs[$type];

    // Check size
    if ($file['size'] > $cfg['max_size']) {
        return ['success' => false, 'error' => 'File exceeds maximum allowed size of ' . round($cfg['max_size'] / (1024 * 1024)) . 'MB.'];
    }

    // Inspect extension
    $origName = $file['name'];
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    // Hard block dangerous extensions
    $forbiddenExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar', 'exe', 'bat', 'sh', 'cmd', 'pl', 'cgi', 'js', 'html', 'htm', 'asp', 'aspx', 'jsp'];
    if (in_array($ext, $forbiddenExts, true)) {
        return ['success' => false, 'error' => 'Execution risk detected. This file type is strictly disallowed.'];
    }

    if (!in_array($ext, $cfg['extensions'], true)) {
        return ['success' => false, 'error' => 'Allowed extensions for ' . $type . ': ' . implode(', ', $cfg['extensions']) . '.'];
    }

    // Inspect MIME type using finfo if available
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if ($mime && !in_array($mime, $cfg['mimes'], true)) {
            // Some MP3s report as audio/x-wav or application/octet-stream
            if ($type !== 'audio' && $type !== 'video') {
                return ['success' => false, 'error' => 'Invalid file format detected (' . htmlspecialchars($mime) . ').'];
            }
        }
    }

    // Destination target directory
    $baseDir = dirname(__DIR__); // Project root
    $targetDir = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $destSubdir);
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            return ['success' => false, 'error' => 'Failed to create storage directory.'];
        }
    }

    // Generate safe unique filename
    $rawBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
    $cleanBase = substr($rawBase, 0, 24);
    $safeName = $cfg['prefix'] . ($cleanBase ? $cleanBase . '_' : '') . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'error' => 'Could not save uploaded file to destination.'];
    }

    // Normalized relative path for DB
    $relPath = rtrim($destSubdir, '/') . '/' . $safeName;

    return [
        'success'   => true,
        'path'      => $relPath,
        'filename'  => $safeName,
        'size'      => $file['size']
    ];
}
