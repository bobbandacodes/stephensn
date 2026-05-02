<?php
require_once __DIR__ . '/config.php';

/**
 * Validate + move an uploaded image. Returns ['filename'=>..., 'error'=>...].
 * filename === null means "no file uploaded" when error is also null.
 */
function handle_image_upload(array $file, int $maxBytes = 5242880): array {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return ['filename' => null, 'error' => null];
    if ($file['error'] !== UPLOAD_ERR_OK)      return ['filename' => null, 'error' => 'Upload failed.'];
    if ($file['size'] > $maxBytes)             return ['filename' => null, 'error' => 'Image too large (max ' . round($maxBytes/1048576) . 'MB).'];

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!isset($allowed[$mime])) return ['filename' => null, 'error' => 'Only JPG, PNG, WebP, or GIF allowed.'];

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        return ['filename' => null, 'error' => 'Could not save upload.'];
    }
    return ['filename' => $name, 'error' => null];
}

function delete_upload(?string $filename): void {
    if (!$filename) return;
    $path = UPLOAD_DIR . '/' . basename($filename);
    if (is_file($path)) @unlink($path);
}
