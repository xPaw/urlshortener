CREATE TABLE `links` (
  `LinkId` int NOT NULL AUTO_INCREMENT,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `Link` mediumtext COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`LinkId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
