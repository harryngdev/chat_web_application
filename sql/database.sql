-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2021 at 06:58 AM
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

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`CHAT_ID`, `ROOM_ID`, `USER_ID`, `CHAT_CONTENT`, `CHAT_TIME`) VALUES
(61, 18, 9, 'Just setting up my app', '2021-03-30 04:54:28'),
(62, 18, 10, 'Hello', '2021-03-30 04:54:33');

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

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`PERSON_ID`, `PERSON_FULLNAME`, `PERSON_EMAIL`, `PERSON_FACE`) VALUES
(11, 'Ha', 'ha@gmail.com', 'Ha'),
(12, 'Thao', 'thao@gmail.com', 'Thao'),
(13, 'Quy', 'quy@gmail.com', 'Quy'),
(14, 'Khanh', 'khanh@gmail.com', 'Khanh'),
(15, 'Nghia', 'nghia@gmail.com', 'Nghia');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `ROOM_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`ROOM_ID`) VALUES
(18),
(19),
(20),
(21);

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

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USER_ID`, `USER_NAME`, `USER_PASSWORD`, `USER_AVT`, `PERSON_ID`, `USER_LASTACTION`) VALUES
(9, 'Ha', '$2y$10$BTAw1V9MBT7eKmy7JYzVqe6TOwOnqnegdeUaW1gwOQjJnSSFe9AWy', 'user2.jpg', 11, '2021-03-30 04:16:42'),
(10, 'Thao', '$2y$10$iPxKw4HYUIZ3/QrXHiR0SuriKyGglSNzXJ6ZpiEbHGC0qdQdcQppO', 'user3.jpg', 12, '2021-03-30 04:22:31'),
(11, 'Quy', '$2y$10$lnWqgehYYmJd4xW1gTjJ9OhxV8Md86u4gywcfhi0mJ4oFsFuPIqe2', 'user5.jpg', 13, '2021-03-30 04:22:48'),
(12, 'Khanh', '$2y$10$QeW.il9L9Y6vPz8z.EGiheA9zW5C86E0JZZTbtkQx0Lh.f2o/lSFC', 'user4.jpg', 14, '2021-03-30 04:23:02'),
(13, 'Nghia', '$2y$10$50nFZWdIQAwTuZeXlHl4POlT7IOTTBnJUw0STUFRj1FoXgq2SGYgK', 'user1.jpg', 15, '2021-03-30 04:23:18');

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
-- Dumping data for table `user_room`
--

INSERT INTO `user_room` (`USER_ID`, `ROOM_ID`, `USER_ROOM_STATUS`, `USER_ROOM_TIME`) VALUES
(9, 18, 0, '2021-03-30 04:54:33'),
(9, 19, 0, '2021-03-30 04:25:25'),
(9, 20, 0, '2021-03-30 04:25:27'),
(9, 21, 0, '2021-03-30 04:25:29'),
(10, 18, 1, '2021-03-30 04:54:33'),
(11, 19, 1, '2021-03-30 04:25:25'),
(12, 20, 1, '2021-03-30 04:25:27'),
(13, 21, 1, '2021-03-30 04:25:29');

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
  MODIFY `CHAT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `PERSON_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `ROOM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
