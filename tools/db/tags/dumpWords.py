#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re

path='/var/www/test/tagging/tools/db/tags/'

wordsFile = open(path+'wordsBare.txt', 'w')

con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb');


with con:

    cur = con.cursor(mdb.cursors.DictCursor)
    cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, '-', words.suffix) else words.text end) as word_text from words order by sentence_id, position")


    lastSentId = -1
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        if row['word_text']:
            if row['sentence_id'] <> lastSentId:
                if lastSentId <> -1:
                    wordsFile.write("\n")
                lastSentId = row['sentence_id']
            
            text = re.sub('\s+','-',row['word_text'])
            wordsFile.write(text+' ')
        
    wordsFile.write("\n")        


wordsFile.close()

