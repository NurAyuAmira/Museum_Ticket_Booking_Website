
USE museum;

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `activity_details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

INSERT INTO activity_logs VALUES("1", "1", "Login attempt", "User Logged In", "2024-05-22 12:26:40");
INSERT INTO activity_logs VALUES("2", "1", "Logout", "User logged out", "2024-05-22 12:27:41");
INSERT INTO activity_logs VALUES("3", "1", "Login attempt", "User Logged In", "2024-05-22 12:29:25");
INSERT INTO activity_logs VALUES("4", "1", "Failed login attempt", "Incorrect password", "2024-05-22 12:29:25");
INSERT INTO activity_logs VALUES("5", "2", "Login attempt", "User Logged In", "2024-05-22 14:26:10");
INSERT INTO activity_logs VALUES("6", "2", "Failed login attempt", "Incorrect password", "2024-05-22 14:26:10");
INSERT INTO activity_logs VALUES("7", "2", "Login attempt", "User Logged In", "2024-05-22 14:27:23");
INSERT INTO activity_logs VALUES("8", "2", "Failed login attempt", "Incorrect password", "2024-05-22 14:27:23");
INSERT INTO activity_logs VALUES("9", "2", "Logout", "User logged out", "2024-05-22 14:29:13");
INSERT INTO activity_logs VALUES("10", "2", "Failed login attempt", "Incorrect password", "2024-05-22 14:37:56");
INSERT INTO activity_logs VALUES("11", "2", "Failed login attempt", "Incorrect password", "2024-05-22 14:37:56");
INSERT INTO activity_logs VALUES("12", "2", "Login attempt", "User Logged In", "2024-05-22 14:38:13");
INSERT INTO activity_logs VALUES("13", "2", "Failed login attempt", "Incorrect password", "2024-05-22 14:38:13");





CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO admins VALUES("2", "admin", "$2y$10$1dwbvmODobu0syMklQmTFO0IRUZHoj/eI9KpX4Ko/l7iLmUmjF8Q.", "hunkmoron7@gmail.com", "2024-05-22 13:47:15");





CREATE TABLE `exhibits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `tickets_available` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO exhibits VALUES("2", "Telecommunication Exhibits", "At Museum Telekom", "2024-05-24", "497", "3.00");

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO users VALUES("2", "ayu", "$2y$10$MXwF2Ha720pOZMTA57Vtg.RcVdFChrlFA3jfR4aauGNfJ0Oih/0LW", "yuzoktober@gmail.com", "2024-05-22 14:26:00");


CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `exhibit_id` int(11) DEFAULT NULL,
  `num_tickets` int(11) NOT NULL,
  `reservation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `invoice_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `exhibit_id` (`exhibit_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`exhibit_id`) REFERENCES `exhibits` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO reservations VALUES("1", "2", "2", "3", "2024-05-22 14:45:38", "NULL");


CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) DEFAULT NULL,
  `cardholder_name` text NOT NULL,
  `card_number` text NOT NULL,
  `expiry_date` text NOT NULL,
  `cvv_number` text NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO payments VALUES("1", "1", "cTlrb2x2L25NVFczZ29YTWZhZExvUDRlQW51bUJ3UklMREV4THUyYU1XMD06OmbMuUBAmnz6lMCRdm82O4E=", "XXXXXXXXXXXX4567", "12/XX", "XXX", "2024-05-22 14:48:24");


CREATE TABLE `traffic_monitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `request_count` int(11) DEFAULT 1,
  `last_request` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

INSERT INTO traffic_monitor VALUES("1", "NULL", "::1", "10", "2024-05-22 14:38:13");
INSERT INTO traffic_monitor VALUES("2", "1", "::1", "10", "2024-05-22 14:38:13");
INSERT INTO traffic_monitor VALUES("3", "2", "::1", "7", "2024-05-22 14:38:13");
INSERT INTO traffic_monitor VALUES("4", "2", "::1", "5", "2024-05-22 14:38:13");
INSERT INTO traffic_monitor VALUES("5", "2", "::1", "1", "2024-05-22 14:38:13");









