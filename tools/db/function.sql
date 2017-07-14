DROP FUNCTION IF EXISTS string_array_size;
DROP FUNCTION IF EXISTS string_array_at;
DROP FUNCTION IF EXISTS within_distance;

DELIMITER $$

CREATE FUNCTION string_array_size(input varchar(500)) RETURNS INT
    DETERMINISTIC
BEGIN
    RETURN LENGTH(input) - LENGTH(REPLACE(input, ',','')) + 1;
END$$

CREATE FUNCTION string_array_at(input varchar(500), i INT) RETURNS INT
    DETERMINISTIC
BEGIN
    RETURN substring_index(substring_index(input,',',i-string_array_size(input)), ',',1);
END$$


CREATE FUNCTION within_distance(positions1str varchar(500), positions2str varchar(500), distance INT, two_way BOOL) RETURNS BOOL
    DETERMINISTIC
BEGIN
    DECLARE index1, index2, size1, size2, pos1, pos2 INT;
    DECLARE found BOOL;

    IF positions1str is null or length(positions1str) = 0 or positions2str is null or length(positions2str) = 0 THEN
        RETURN false;
    ELSEIF distance = 0 THEN
        RETURN true;
    ELSE
        SET index1 = 0;
        SET size1 = string_array_size(positions1str);
        SET size2 = string_array_size(positions2str);

        WHILE index1 < size1 DO
            SET pos1 = string_array_at(positions1str, index1);
            SET index2 = 0;
            WHILE index2 < size2 DO
                SET pos2 = string_array_at(positions2str, index2);

                IF pos2 > pos1 AND pos2 <= pos1 + distance THEN
                    RETURN true;
                END IF;
                IF two_way AND pos2 >= pos1 - distance AND pos2 < pos1 THEN
                    RETURN true;
                END IF;
                SET index2 = index2 + 1;
            END WHILE;

            SET index1 = index1 + 1;
        END WHILE;
        RETURN false;
    END IF;
END$$

DELIMITER ;
