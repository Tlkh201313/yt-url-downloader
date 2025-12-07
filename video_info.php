<?php

// Respond with JSON
function send_json($data) {
    header("Content-Type: application/json");
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Validate URL
$url = $_GET["video_url"] ?? null;
if (!$url) {
    send_json(["error" => "Missing video_url parameter"]);
}

// Run yt-dlp to fetch formats (JSON output)
exec("yt-dlp -J " . escapeshellarg($url) . " 2>&1", $output, $status);

if ($status !== 0) {
    send_json([
        "error" => "yt-dlp failed",
        "details" => implode("\n", $output)
    ]);
}

$json = implode("\n", $output);
$data = json_decode($json, true);

// yt-dlp returned invalid JSON
if (!$data) {
    send_json([
        "error" => "Failed to parse yt-dlp JSON",
        "raw"   => $json
    ]);
}

// Return available formats only
send_json([
    "title"   => $data["title"] ?? "Unknown",
    "formats" => $data["formats"] ?? []
]);
