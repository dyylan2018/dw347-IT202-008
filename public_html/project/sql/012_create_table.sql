CREATE TABLE MyPlayers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    player_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, player_id),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (player_id) REFERENCES Players(id)
);