DROP DATABASE IF EXISTS `prwb_2021_g05`;
CREATE DATABASE IF NOT EXISTS `prwb_2021_g05` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `prwb_2021_g05`;

CREATE TABLE `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Mail` varchar(128) NOT NULL,
  `FullName` varchar(128) NOT NULL,
  `Password` varchar(256) NOT NULL,
  `RegisteredAt` datetime NOT NULL DEFAULT current_timestamp(),
  `Role` ENUM('user', 'admin') DEFAULT 'user',
  PRIMARY KEY(`ID`),
  UNIQUE(`Mail`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE `board` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(128) NOT NULL,
  `Owner` int(11) NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `ModifiedAt` datetime NULL,
  PRIMARY KEY(`ID`),
  FOREIGN KEY(`Owner`) REFERENCES `User`(`ID`),
  UNIQUE(`Title`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `column` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(128) NOT NULL,
  `Position` int(11) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `ModifiedAt` datetime NULL,
  `Board` int(11) NOT NULL,
  PRIMARY KEY(`ID`),
  FOREIGN KEY(`Board`) REFERENCES `Board`(`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `card` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(128) NOT NULL,
  `Body` text NOT NULL,
  `Position` int(11) NOT NULL DEFAULT 0,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `ModifiedAt` datetime NULL,
  `Author` int(11) NOT NULL,
  `Column` int(11) NOT NULL,
  `DueDate` DATE NULL,
  PRIMARY KEY(`ID`),
  FOREIGN KEY(`Author`) REFERENCES `User`(`ID`),
  FOREIGN KEY(`Column`) REFERENCES `Column`(`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
  
CREATE TABLE `comment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Body` text NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `ModifiedAt` datetime NULL,
  `Author` int(11) NOT NULL,
  `Card` int(11) NOT NULL,
  PRIMARY KEY(`ID`),
  FOREIGN KEY(`Author`) REFERENCES `User`(`ID`),
  FOREIGN KEY(`Card`) REFERENCES `Card`(`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


CREATE TABLE collaborate(
    Board INT(11) NOT NULL,
    Collaborator INT(11) NOT NULL,
    PRIMARY KEY (Board, Collaborator),
    FOREIGN KEY(Collaborator) REFERENCES User(ID),
    FOREIGN KEY(Board) REFERENCES Board(ID)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE participate(
    Participant INT(11) NOT NULL,
    Card INT(11) NOT NULL,
    PRIMARY KEY(Participant, Card),
    FOREIGN KEY(Card) REFERENCES Card(ID),
    FOREIGN KEY(Participant) REFERENCES User(ID)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;