<?php

// Must pass a video_url
$url = $_GET["video_url"] ?? null;
if (!$url) {
    die("Missing video_url parameter");
}

// Must pass format_id (e.g., 18, 22, 137, 251, etc.)
$format = $_GET["format_id"] ?? null;
if (!$format) {
    die("Missing format_id parameter");
}

// Safe temp file
$tmpFile = "/tmp/video_" . uniqid() . ".mp4";

// Download chosen format
$cmd = "yt-dlp -f " . escapeshellarg($format) . " -o " . escapeshellarg($tmpFile) . " " . escapeshellarg($url) . " 2>&1";
exec($cmd, $output, $status);

if ($status !== 0 || !file_exists($tmpFile)) {
    die("Download failed: " . implode("\n", $output));
}

// Output file to browser
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=video.mp4");
header("Content-Length: " . filesize($tmpFile));
readfile($tmpFile);

// Remove temp file
unlink($tmpFile);
exit;
