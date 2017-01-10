#!/bin/sh

echo "Recreating database"
echo "Purging db and applying new model"
mysql --user=webuser --password=tialof --default-character-set=utf8 < testtaggingdb.sql

for initFile in `ls iatagger_init/*.sql`
do
	echo "Inserting data from init file:" $initFile
	mysql --user=webuser --password=tialof --default-character-set=utf8 testtaggingdb < $initFile
done
