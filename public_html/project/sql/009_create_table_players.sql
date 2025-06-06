CREATE TABLE IF NOT EXISTS `Players`
(
    `id` INT PRIMARY KEY,
    `sport_id` INT,
    `team_id` INT,
    `updated_at` DATETIME,
    `first_name` VARCHAR(100),
    `last_name` VARCHAR(100),
    `display_name` VARCHAR(100),
    `weight` INT,
    `height` INT,
    `display_weight` VARCHAR(20),
    `display_height` VARCHAR(20),
    `age` INT,
    `date_of_birth` DATETIME,
    `slug` VARCHAR(100),
    `jersey` VARCHAR(10),
    `position` VARCHAR(100),
    `position_abbreviation` VARCHAR(10),
    `debut_year` INT,
    `birth_place_city` VARCHAR(100),
    `birth_place_country` VARCHAR(100),
    `experience_years` INT,
    `active` BOOLEAN,
    `status` VARCHAR(20),
    `bats` VARCHAR(10),
    `throws` VARCHAR(10)
)