<?php
// FIX 1: CORS Header - Allows the frontend (GitHub Pages) to communicate with the local backend (localhost:5000)
header("Access-Control-Allow-Origin: *"); 

// Always return JSON
function send_json($data) {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$url = $_GET["video_url"] ?? null;
if (!$url) {
    send_json(["error" => "Missing video_url parameter"]);
}

$command = "yt-dlp --no-playlist --no-warnings -J " . escapeshellarg($url) . " 2>/dev/null";
exec($command, $output, $status);

if ($status !== 0) {
    send_json([
        "error" => "yt-dlp failed to fetch data",
        "details" => "Process returned non-zero exit status ($status)"
    ]);
}

$json = implode("\n", $output);
$data = json_decode($json, true);

if ($data === null) {
    send_json([
        "error" => "Failed to parse yt-dlp JSON",
        "raw" => $json
    ]);
}

send_json([
    "title" => $data["title"] ?? "Unknown Video Title",
    "formats" => $data["formats"] ?? []
]);