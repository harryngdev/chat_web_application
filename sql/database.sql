-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2021 at 01:09 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo_app_chat`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `addChat` (IN `roomid` INT, IN `userid` INT, IN `content` TEXT CHARSET utf8mb4)  NO SQL
BEGIN
    START TRANSACTION
        ;
    INSERT INTO `chat`(
        `chat`.`ROOM_ID`,
        `chat`.`USER_ID`,
        `chat`.`CHAT_CONTENT`
    )
    VALUES(roomid, userid, content);
    UPDATE
        `user_room`
    SET
        `user_room`.`USER_ROOM_TIME` = TIMESTAMP(NOW())
    WHERE
        `user_room`.`ROOM_ID` = roomid;
    COMMIT
        ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addRoom` (IN `user1id` INT, IN `user2id` INT)  NO SQL
BEGIN
    START TRANSACTION
        ;
    INSERT INTO `room`
    VALUES();
    SELECT
        @id := LAST_INSERT_ID();
    INSERT INTO `user_room`(
        `user_room`.`USER_ID`,
        `user_room`.`ROOM_ID`,
        `user_room`.`USER_ROOM_STATUS`
    )
    VALUES(user1id, @id, 0);
    INSERT INTO `user_room`(
        `user_room`.`USER_ID`,
        `user_room`.`ROOM_ID`,
        `user_room`.`USER_ROOM_STATUS`
    )
    VALUES(user2id, @id, 1);
    COMMIT
        ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkFriend` (IN `userid` INT, IN `friendid` INT)  NO SQL
BEGIN
    SELECT
        *
    FROM
        (
        SELECT
            `user_room`.`ROOM_ID`,
            `user_room`.`USER_ID`,
            `user`.`USER_NAME`,
            `user_room`.`USER_ROOM_STATUS`,
            `user_room`.`USER_ROOM_TIME`
        FROM
            (
            SELECT
                `user_room`.`ROOM_ID` AS room_id
            FROM
                `user_room`
            WHERE
                `user_room`.`USER_ID` = userid
        ) AS a
    INNER JOIN `user_room` ON(a.room_id = `user_room`.`ROOM_ID`)
    INNER JOIN `user` ON(
            `user_room`.`USER_ID` = `user`.`USER_ID`
        )
    WHERE NOT
        (`user_room`.`USER_ID` = userid)
    ) AS b
    WHERE
        b.`USER_ID` = friendid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `checkLogin` (IN `username` TEXT CHARSET utf8mb4)  NO SQL
BEGIN
	SELECT * FROM `user` 
    WHERE `user`.`USER_NAME` = username;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `register` (IN `username` TEXT CHARSET utf8mb4, IN `password` TEXT CHARSET utf8mb4, IN `fullname` TEXT CHARSET utf8mb4, IN `email` TEXT CHARSET utf8mb4, IN `face` TEXT CHARSET utf8mb4, IN `avt` VARCHAR(255) CHARSET utf8mb4)  NO SQL
    SQL SECURITY INVOKER
BEGIN
    START TRANSACTION
        ;
    INSERT INTO `person`(
        `person`.`PERSON_FULLNAME`,
        `person`.`PERSON_EMAIL`,
        `person`.`PERSON_FACE`
    )
    VALUES(
        fullname,
        (SELECT 
         	LOWER(email)
   	),
        face
    );
    INSERT INTO `user`(
        `user`.`USER_NAME`,
        `user`.`USER_PASSWORD`,
        `user`.`USER_AVT`,
        `user`.`PERSON_ID`,
        `user`.`USER_LASTACTION`
    )
    VALUES(
        username,
        password,
        avt,
        LAST_INSERT_ID(),
        TIMESTAMP(NOW()));
    
    COMMIT
        ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchFriend` (IN `userid` INT, IN `s` TEXT CHARSET utf8mb4)  NO SQL
BEGIN
    SELECT
        `user_room`.`ROOM_ID`,
        `user_room`.`USER_ID`,
        `user`.`USER_NAME`,
        `user`.`USER_AVT`,
        `user_room`.`USER_ROOM_STATUS`,
        `user_room`.`USER_ROOM_TIME`
    FROM
        (
        SELECT
            `user_room`.`ROOM_ID` AS room_id
        FROM
            `user_room`
        WHERE
            `user_room`.`USER_ID` = userid
    ) AS a
    INNER JOIN `user_room` ON
        (a.room_id = `user_room`.`ROOM_ID`)
    INNER JOIN `user` ON
        (
            `user_room`.`USER_ID` = `user`.`USER_ID`
        )
    WHERE NOT
        (`user_room`.`USER_ID` = userid) AND `user`.`USER_NAME` like CONCAT("%",s,"%")
    ORDER BY
        `user_room`.`USER_ROOM_TIME`
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selectLast5Chat` (IN `roomid` INT, IN `chatid` INT)  NO SQL
BEGIN
    SELECT
        *
    FROM
        `chat`
    WHERE
        `chat`.`ROOM_ID` = roomid AND `chat`.`CHAT_ID` < chatid
    ORDER BY
        `chat`.`CHAT_ID`
    DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selectNewestChat` (IN `roomid` INT, IN `chatid` INT)  NO SQL
BEGIN
    SELECT
        *
    FROM
        `chat`,`user`
    WHERE
    	`chat`.`USER_ID` = `user`.`USER_ID` AND
        `chat`.`ROOM_ID` = roomid AND `chat`.`CHAT_ID` > chatid
    ORDER BY
        `chat`.`CHAT_ID`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selectNewestFriend` (IN `userid` INT, IN `time` TIMESTAMP)  NO SQL
BEGIN
    SELECT
        `user_room`.`ROOM_ID`,
        `user_room`.`USER_ID`,
        `user`.`USER_NAME`,
        `user`.`USER_AVT`,
        `user_room`.`USER_ROOM_STATUS`,
        `user_room`.`USER_ROOM_TIME`
    FROM
        (
        SELECT
            `user_room`.`ROOM_ID` AS room_id
        FROM
            `user_room`
        WHERE
            `user_room`.`USER_ID` = userid
    ) AS a
    INNER JOIN `user_room` ON
        (a.room_id = `user_room`.`ROOM_ID`)
    INNER JOIN `user` ON
        (
            `user_room`.`USER_ID` = `user`.`USER_ID`
        )
    WHERE NOT
        (`user_room`.`USER_ID` = userid) AND `user_room`.`USER_ROOM_TIME` > time
    ORDER BY
        `user_room`.`USER_ROOM_TIME`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selectTop10Chat` (IN `roomid` INT)  NO SQL
BEGIN
    SELECT
        *
    FROM
        `chat`, `user`
    WHERE
    	`chat`.`USER_ID` = `user`.`USER_ID` AND
        `chat`.`ROOM_ID` = roomid
    ORDER BY
    	`chat`.`CHAT_ID`
    DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selectTop10Friend` (IN `userid` INT)  NO SQL
BEGIN
    SELECT
        `user_room`.`ROOM_ID`,
        `user_room`.`USER_ID`,
        `user`.`USER_NAME`,
        `user`.`USER_AVT`,
        `user_room`.`USER_ROOM_STATUS`,
        `user_room`.`USER_ROOM_TIME`
    FROM
        (
        SELECT
            `user_room`.`ROOM_ID` AS room_id
        FROM
            `user_room`
        WHERE
            `user_room`.`USER_ID` = userid
    ) AS a
    INNER JOIN `user_room` ON
        (a.room_id = `user_room`.`ROOM_ID`)
    INNER JOIN `user` ON
        (
            `user_room`.`USER_ID` = `user`.`USER_ID`
        )
    WHERE NOT
        (`user_room`.`USER_ID` = userid)
    ORDER BY
        `user_room`.`USER_ROOM_TIME`
    DESC
    LIMIT 10;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserRoomStatus` (IN `userid` INT, IN `roomid` INT, IN `status` INT)  NO SQL
BEGIN
    UPDATE
        `user_room`
    SET
        `user_room`.`USER_ROOM_STATUS` = status
    WHERE
        `user_room`.`USER_ID` = userid AND `user_room`.`ROOM_ID` = roomid;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `CHAT_ID` int(11) NOT NULL,
  `ROOM_ID` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `CHAT_CONTENT` text DEFAULT NULL,
  `CHAT_TIME` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `PERSON_ID` int(11) NOT NULL,
  `PERSON_FULLNAME` text DEFAULT NULL,
  `PERSON_EMAIL` text DEFAULT NULL,
  `PERSON_FACE` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `ROOM_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USER_ID` int(11) NOT NULL,
  `USER_NAME` text DEFAULT NULL,
  `USER_PASSWORD` text DEFAULT NULL,
  `USER_AVT` varchar(255) NOT NULL,
  `PERSON_ID` int(11) DEFAULT NULL,
  `USER_LASTACTION` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_room`
--

CREATE TABLE `user_room` (
  `USER_ID` int(11) NOT NULL,
  `ROOM_ID` int(11) NOT NULL,
  `USER_ROOM_STATUS` int(11) DEFAULT NULL,
  `USER_ROOM_TIME` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`CHAT_ID`),
  ADD KEY `USER_ID` (`ROOM_ID`,`USER_ID`),
  ADD KEY `USER_ID_2` (`USER_ID`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`PERSON_ID`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`ROOM_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USER_ID`),
  ADD KEY `FK_USER_PERSON` (`PERSON_ID`);

--
-- Indexes for table `user_room`
--
ALTER TABLE `user_room`
  ADD PRIMARY KEY (`USER_ID`,`ROOM_ID`) USING BTREE,
  ADD KEY `FK_ROOM_USER_ROOM` (`ROOM_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `CHAT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `PERSON_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `ROOM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user_room` (`USER_ID`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`ROOM_ID`) REFERENCES `user_room` (`ROOM_ID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`PERSON_ID`) REFERENCES `person` (`PERSON_ID`);

--
-- Constraints for table `user_room`
--
ALTER TABLE `user_room`
  ADD CONSTRAINT `user_room_ibfk_1` FOREIGN KEY (`ROOM_ID`) REFERENCES `room` (`ROOM_ID`),
  ADD CONSTRAINT `user_room_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
