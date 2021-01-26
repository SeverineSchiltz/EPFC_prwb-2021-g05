INSERT INTO `user` VALUES (6,'severine@test.be','Severine','56ce92d1de4f05017cf03d6cd514d6d1','2020-10-11 17:46:19'),(7,'sinouhe@test.be','Sinouhe','56ce92d1de4f05017cf03d6cd514d6d1','2020-10-11 17:46:19');

DROP PROCEDURE IF EXISTS add100BCC;
DELIMITER $$
CREATE PROCEDURE add100BCC()
BEGIN
    DECLARE counter INT DEFAULT 1;
    DECLARE counterCard INT;
    WHILE counter <= 100 DO
		INSERT INTO `board` (Title, Owner, CreatedAt) VALUES (concat('testboard', counter),7,'2021-01-11 17:48:59');
        INSERT INTO `column` (Title, Board, Position, CreatedAt) VALUES (concat('testcolumn', counter),5, counter, '2021-01-11 17:48:59');
        SET counterCard = 1;
		IF MOD(counter, 25) = 0 OR counter = 1 THEN
			WHILE counterCard <= 100 DO
				INSERT INTO `card` (Title, Position, Body, CreatedAt, Author, `Column`)
				VALUES (concat('testcarte', ((counter-1)*100)+counterCard), counterCard,'blabla!','2021-01-11 17:56:40',6,counter+15);
				SET counterCard = counterCard + 1;
			END WHILE;
		END IF;
        SET counter = counter + 1;
    END WHILE;
END$$
DELIMITER ;
CALL add100BCC;