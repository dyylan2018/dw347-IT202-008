CREATE TABLE IF NOT EXISTS UserPlayerAssociations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    player_id INT NOT NULL,
    UNIQUE(user_id, player_id),
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES Players(id) ON DELETE CASCADE
);