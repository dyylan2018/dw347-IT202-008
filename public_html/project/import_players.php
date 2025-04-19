<?php
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/api_helper.php");
require_once(__DIR__ . "/../../lib/functions.php");

// Set your API endpoint
$endpoint = "https://therundown-therundown-v1.p.rapidapi.com/v2/teams/48/players";

// Call the API using the correct key name from .env
$response = get($endpoint, "STOCK_API_KEY", [], true, "therundown-therundown-v1.p.rapidapi.com");

// Decode JSON
$data = json_decode($response["response"], true);

// Ensure response has players
if (!isset($data["players"]) || !is_array($data["players"])) {
    echo "No players found or invalid API response.";
    exit;
}

$db = getDB();
$count = 0;

foreach ($data["players"] as $player) {
    if (insertPlayer($player, $db)) {
        $count++;
    }
}

echo "Successfully imported {$count} players.";