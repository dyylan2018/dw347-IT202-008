<?php
require_once(__DIR__ . "/db.php");

$BASE_PATH = '/project';

require(__DIR__ . "/flash_messages.php");

require(__DIR__ . "/safer_echo.php");

require(__DIR__ . "/sanitizers.php");

require(__DIR__ . "/user_helpers.php");

require(__DIR__ . "/duplicate_user_details.php");

require(__DIR__ . "/reset_session.php");

require(__DIR__ . "/get_url.php");

require_once(__DIR__ . "/api_helper.php");

require(__DIR__ . "/stock_api.php");

function insertPlayer($player, $db) {
    try {
        // Convert datetime fields to MySQL format
        $updatedAtRaw = $player["updated_at"] ?? null;
        $updatedAt = $updatedAtRaw ? (new DateTime($updatedAtRaw))->format("Y-m-d H:i:s") : null;

        $dobRaw = $player["date_of_birth"] ?? null;
        $dob = $dobRaw ? (new DateTime($dobRaw))->format("Y-m-d H:i:s") : null;

        $stmt = $db->prepare("
            INSERT INTO Players (
                id, sport_id, team_id, updated_at, first_name, last_name,
                display_name, weight, height, display_weight, display_height,
                age, date_of_birth, slug, jersey, position, position_abbreviation,
                debut_year, birth_place_city, birth_place_country,
                experience_years, active, status, bats, throws
            )
            VALUES (
                :id, :sport_id, :team_id, :updated_at, :first_name, :last_name,
                :display_name, :weight, :height, :display_weight, :display_height,
                :age, :date_of_birth, :slug, :jersey, :position, :position_abbreviation,
                :debut_year, :birth_place_city, :birth_place_country,
                :experience_years, :active, :status, :bats, :throws
            )
            ON DUPLICATE KEY UPDATE
                updated_at = VALUES(updated_at),
                first_name = VALUES(first_name),
                last_name = VALUES(last_name),
                display_name = VALUES(display_name),
                weight = VALUES(weight),
                height = VALUES(height),
                display_weight = VALUES(display_weight),
                display_height = VALUES(display_height),
                age = VALUES(age),
                date_of_birth = VALUES(date_of_birth),
                slug = VALUES(slug),
                jersey = VALUES(jersey),
                position = VALUES(position),
                position_abbreviation = VALUES(position_abbreviation),
                debut_year = VALUES(debut_year),
                birth_place_city = VALUES(birth_place_city),
                birth_place_country = VALUES(birth_place_country),
                experience_years = VALUES(experience_years),
                active = VALUES(active),
                status = VALUES(status),
                bats = VALUES(bats),
                throws = VALUES(throws)
        ");

        $stmt->execute([
            ":id" => $player["id"],
            ":sport_id" => $player["sport_id"],
            ":team_id" => $player["team_id"],
            ":updated_at" => $updatedAt,
            ":first_name" => $player["first_name"] ?? null,
            ":last_name" => $player["last_name"] ?? null,
            ":display_name" => $player["display_name"] ?? null,
            ":weight" => is_numeric($player["weight"]) ? (int)$player["weight"] : null,
            ":height" => is_numeric($player["height"]) ? (int)$player["height"] : null,
            ":display_weight" => $player["display_weight"] ?? null,
            ":display_height" => $player["display_height"] ?? null,
            ":age" => is_numeric($player["age"]) ? (int)$player["age"] : null,            ":date_of_birth" => $dob,
            ":slug" => $player["slug"] ?? null,
            ":jersey" => is_numeric($player["jersey"]) ? (int)$player["jersey"] : null,            ":position" => $player["position"] ?? null,
            ":position_abbreviation" => $player["position_abbreviation"] ?? null,
            ":debut_year" => $player["debut_year"] ?? null,
            ":birth_place_city" => $player["birth_place_city"] ?? null,
            ":birth_place_country" => $player["birth_place_country"] ?? null,
            ":experience_years" => is_numeric($player["experience_years"]) ? (int)$player["experience_years"] : null,            ":active" => is_numeric($player["active"]) ? (int)$player["active"] : null,
            ":status" => $player["status"] ?? null,
            ":bats" => $player["bats"] ?? null,
            ":throws" => $player["throws"] ?? null
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Failed to insert player: " . $e->getMessage());
        return false;
    }
}
?>