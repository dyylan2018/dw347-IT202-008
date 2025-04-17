<?php
/**
 * This file is a wrapper for our API calls.
 * Here, each endpoint needed will be exposes as a function.
 * The function will take the parameters needed for the API call and return the result.
 * The function will also handle the API key and endpoint.
 * Requires the api_helper.php file and load_api_keys.php file.
 */

/**
 * Fetches the stock quote for a given symbol.
 */
function fetch_quote($team_id = 48)
{
    $endpoint = "https://therundown-therundown-v1.p.rapidapi.com/v2/teams/{$team_id}/players";
    $data = []; // No query parameters needed for this endpoint
    $isRapidAPI = true;
    $rapidAPIHost = "therundown-therundown-v1.p.rapidapi.com";

    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    
    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

    $transformedResult = [];

    if (isset($result['results'])) {
        foreach ($result['results'] as $player) {
            $transformedResult[] = [
                'id' => $player['id'] ?? null,
                'display_name' => $player['display_name'] ?? '',
                'jersey' => $player['jersey'] ?? '',
                'position' => $player['position'] ?? '',
            ];
        }
    }

    return $transformedResult;
}
