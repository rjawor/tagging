#!/bin/sh

echo "Recreating database"
echo "Purging db and applying new model"
mysql --user=webuser --password=tialof < taggingdb.sql

for initFile in `ls init/*`
do
	echo "Inserting data from init file:" $initFile
	mysql --user=webuser --password=tialof taggingdb < $initFile
done

