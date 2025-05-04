CREATE TABLE IF NOT EXISTS Favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    player_id INT NOT NULL,
    display_name VARCHAR(255),
    jersey VARCHAR(10),
    position VARCHAR(50),
    age INT,
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES Players(id) ON DELETE CASCADE
);