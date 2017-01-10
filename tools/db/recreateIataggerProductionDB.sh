#!/bin/bash

echo "Recreating production database"

read -p "Are you sure? " -n 1 -r
echo    # (optional) move to a new line
if [[ $REPLY =~ ^[Yy]$ ]]
then

    echo "Purging db and applying new model"
    mysql --user=webuser --password=tialof --default-character-set=utf8 < taggingdb.sql
fi
