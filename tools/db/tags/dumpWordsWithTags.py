#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
import utils

path='/var/www/test/tagging/tools/db/tags/'

tagset = utils.getTagset()

wordsFile = open(path+'words.txt', 'w')
con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb');


with con:

    cur = con.cursor(mdb.cursors.DictCursor)
    cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, '-', words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")


    lastSentId = -1
    for i in range(cur.rowcount):        
        row = cur.fetchone()
	if row['word_text']:
	    if row['sentence_id'] <> lastSentId:
                if lastSentId <> -1:
                    wordsFile.write("\n")
                lastSentId = row['sentence_id']
            tag = None
            if row['tag']:
                choiceIds = set(row['tag'].split(','))
                tag = utils.getTag(tagset, choiceIds)

            if tag is None:
                tag = 'empty'
        
            text = re.sub('\s+','-',row['word_text'])
            wordsFile.write(text+'_'+tag+' ')
        
    wordsFile.write("\n")        

wordsFile.close()

