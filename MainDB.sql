-- CREATE DATABASE ICT_Community;
USE ICT_Community;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255),
    role ENUM('student','faculty','alumni'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD is_verified TINYINT DEFAULT 0;
select * from users;
