-- mirror the target table with a temporary table
CREATE TEMPORARY TABLE ImportTemp LIKE FinalDestination;
 
-- load data from a CSV file, BUT translate the contents on the fly
-- this could be accomplished with an external script, but this is simple
-- and more direct
   LOAD DATA
LOCAL INFILE '/path/to/file.csv'
  INTO TABLE ImportTemp
      FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"' ESCAPED BY '\\'
       LINES TERMINATED BY '\n' STARTING BY '' IGNORE 0 LINES
             (number, notebook, @proj, @recv, description)
         SET dateReceived = COALESCE(STR_TO_DATE(@recv, '%c/%e/%Y'), ''), 
             projectID = (SELECT CASE WHEN '' = @proj THEN '' ELSE GROUP_CONCAT(ID) END FROM Projects WHERE number=@proj GROUP BY number)
;
 
-- cleanup the imported data anyway that's necessary
DELETE FROM ImportTemp WHERE '' IN (projectID,dateReceived);
 
-- now do the insert into the actual table
INSERT INTO FinalDestination SELECT * FROM ImportTemp;
 
DROP TEMPORARY TABLE ImportTemp;

