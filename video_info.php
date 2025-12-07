<?php

// Return JSON + exit
function send_json($data) {
    header("Content-Type: application/json");
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// ----------------------
// Validate Input
// ----------------------
$url = isset($_GET["video_url"]) ? trim($_GET["video_url"]) : null;

if (!$url) {
    send_json([
        "error" => "Missing video_url parameter"
    ]);
}

// ----------------------
// Build yt-dlp Command
// ----------------------
//
// -J → JSON metadata output
// --no-playlist → faster
// --no-warnings → keeps output clean
// 2>/dev/null → suppress stderr so JSON is clean
//
$command = "yt-dlp --no-playlist --no-warnings -J " . escapeshellarg($url) . " 2>/dev/null";

// Execute yt-dlp
$output = [];
$status = 0;
exec($command, $output, $status);

// ----------------------
// Check command success
// ----------------------
if ($status !== 0) {
    send_json([
        "error"   => "yt-dlp failed to fetch data",
        "status"  => $status,
        "details" => "Check Docker logs: docker logs php"
    ]);
}

// Combine yt-dlp output
$json = implode("\n", $output);

// ----------------------
// Attempt to decode JSON
// ----------------------
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_json([
        "error"      => "Failed to parse yt-dlp JSON",
        "json_error" => json_last_error_msg(),
        "raw"        => $json
    ]);
}

// ----------------------
// Valid Output
// ----------------------
send_json([
    "title"   => $data["title"]   ?? "Unknown Video Title",
    "formats" => $data["formats"] ?? []
]);

?>
