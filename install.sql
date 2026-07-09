DROP TABLE IF EXISTS predictions;
DROP TABLE IF EXISTS participants;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS rounds;

CREATE TABLE rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    points DECIMAL(4,1) NOT NULL,
    status ENUM('closed','open') NOT NULL DEFAULT 'closed'
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_id INT NOT NULL,
    match_no INT NOT NULL,
    team1 VARCHAR(100) NOT NULL,
    team2 VARCHAR(100) NOT NULL,
    correct_team VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
);

CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    round_id INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_round_roll (round_id, roll),
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
);

CREATE TABLE starting_scores (
    roll VARCHAR(50) PRIMARY KEY,
    base_score DECIMAL(6,1) NOT NULL DEFAULT 0
);

CREATE TABLE predictions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    match_id INT NOT NULL,
    selected_team VARCHAR(100) NOT NULL,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
);

INSERT INTO rounds (code, name, points, status) VALUES
('r16', 'Round of 16', 2, 'open'),
('qf', 'Quarter Final', 3, 'closed'),
('sf', 'Semi Final', 4, 'closed'),
('final', 'Final', 6, 'closed'),
('third', 'Third Place Match', 5, 'closed');

INSERT INTO matches (round_id, match_no, team1, team2) VALUES
(1, 1, 'Canada', 'Morocco'),
(1, 2, 'France', 'Paraguay'),
(1, 3, 'USA', 'Belgium'),
(1, 4, 'Portugal', 'Spain'),
(1, 5, 'Brazil', 'Norway'),
(1, 6, 'Mexico', 'England'),
(1, 7, 'Switzerland', 'Colombia'),
(1, 8, 'Argentina', 'Egypy'),
(2, 1, 'France', 'Morocco'),
(2, 2, 'Spain', 'Belgium'),
(2, 3, 'Norway', 'England'),
(2, 4, 'Switzerland', 'Argentina'),
(3, 1, 'SF Team A', 'SF Team B'),
(3, 2, 'SF Team C', 'SF Team D'),
(4, 1, 'Finalist A', 'Finalist B'),
(5, 1, 'Team A', 'Team B');

INSERT INTO starting_scores (roll, base_score) VALUES
('89043', 14),
('89026', 13),
('89242', 12),
('89027', 12),
('89201', 12),
('89014', 13),
('89152', 13),
('89154', 14),
('89127', 12),
('89022', 11),
('89050', 11),
('89058', 13),
('89142', 12),
('89048', 9),
('89260', 11),
('89230', 11),
('89235', 13),
('89212', 10),
('89150', 10),
('89032', 11),
('89015', 14),
('89222', 12),
('89007', 9),
('89106', 13),
('89211', 0),
('89019', 0),
('89144', 0),
('89262', 0),
('89040', 0),
('89114', 0),
('89248', 0),
('89035', 0),
('89256', 0),
('89055', 0),
('89264', 12);