<?php
/**
 * Serve generated frontend bundles with gzip and immutable caching while
 * preserving the cache directory URL for relative CSS assets.
 */
$type = $_GET['t'] ?? '';
$key = $_GET['f'] ?? '';

if (!in_array($type, ['css', 'js'], true) || !preg_match('/^[a-f0-9]{32}$/', $key)) {
    http_response_code(400);
    exit;
}

$file = __DIR__ . '/' . $key . '.' . $type;
if (!is_file($file)) {
    http_response_code(404);
    exit;
}

$modified = filemtime($file);
$etag = '"' . $key . '-' . $modified . '"';
header('Content-Type: ' . ($type === 'css' ? 'text/css' : 'application/javascript') . '; charset=UTF-8');
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modified) . ' GMT');
header('Vary: Accept-Encoding');

if (trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

$content = file_get_contents($file);
$acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
if (strpos($acceptEncoding, 'gzip') !== false && function_exists('gzencode')) {
    $content = gzencode($content, 7);
    header('Content-Encoding: gzip');
}
header('Content-Length: ' . strlen($content));
echo $content;
