#!/bin/sh

echo "Recreating database"
echo "Purging db and applying new model"
mysql --user=webuser --password=tialof --default-character-set=utf8 < taggingdb.sql

for initFile in `ls init/*.sql`
do
	echo "Inserting data from init file:" $initFile
	mysql --user=webuser --password=tialof --default-character-set=utf8 taggingdb < $initFile
done

