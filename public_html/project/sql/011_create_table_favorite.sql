CREATE TABLE Favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    jersey VARCHAR(50) NULL,
    display_name VARCHAR(255) NULL,
    status VARCHAR(50) NULL,
    position VARCHAR(50) NULL,
    age INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES Players(id) ON DELETE CASCADE
);
