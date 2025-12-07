<?php

// Must pass a video_url
$url = $_GET["video_url"] ?? null;
if (!$url) {
    die("Missing video_url parameter");
}

// Get the filename from the frontend or default to 'video'
$filename = $_GET["filename"] ?? "video";
// Sanitize filename to prevent security issues and illegal characters
$filename = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $filename);
$ext = "mp4"; 

// The format_id passed from the frontend (e.g., 137, 248) specifies the video quality level.
$format_id = $_GET["format_id"] ?? "bestvideo"; 

// Safe temp file
$tmpFile = "/tmp/" . uniqid() . ".mp4";

// --- FIX: Robust Merge Command ---
// -f [ID]+bestaudio: Merges the selected video stream with the best audio stream.
// --merge-output-format mp4: Ensures the final file is MP4.
// --downloader ffmpeg: Explicitly uses ffmpeg for the merging step.
$cmd = "yt-dlp -f " . escapeshellarg($format_id . "+bestaudio") . " --merge-output-format mp4 -o " . escapeshellarg($tmpFile) . " --downloader ffmpeg " . escapeshellarg($url) . " 2>&1";

exec($cmd, $output, $status);

if ($status !== 0 || !file_exists($tmpFile)) {
    // If download fails, check the actual error output from yt-dlp
    $errorMessage = "Download failed (Status: $status).";
    if (!empty($output)) {
        // Only show a limited amount of output to prevent huge error messages
        $errorMessage .= " Details: " . implode(" ", array_slice($output, -5)); 
    }
    die($errorMessage);
}

// Output file to browser
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . urlencode($filename . "." . $ext));
header("Content-Length: " . filesize($tmpFile));
readfile($tmpFile);

// Remove temp file
unlink($tmpFile);
exit;