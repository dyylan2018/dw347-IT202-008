<?php 
function fetch_quote()
{
    $team = isset($_GET["team"]) ? (int)$_GET["team"] : 48; // default to 48 if not provided
    $endpoint = "https://therundown-therundown-v1.p.rapidapi.com/v2/teams/{$team}/players";
    $data = []; // no query parameters
    $isRapidAPI = true;
    $rapidAPIHost = "therundown-therundown-v1.p.rapidapi.com";

    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $decoded = json_decode($result["response"], true);
        $players = $decoded["results"] ?? [];

        $filtered = [];
        $search = strtolower($_GET["player"] ?? "");

        foreach ($players as $player) {
            $name = strtolower($player["display_name"] ?? "");
            if (empty($search) || strpos($name, $search) !== false) {
                $filtered[] = [
                    "id" => $player["id"] ?? null,
                    "display_name" => $player["display_name"] ?? '',
                    "jersey" => $player["jersey"] ?? '',
                    "position" => $player["position"] ?? ''
                ];
            }
        }

        return $filtered;
    }

    return [];
}
?>