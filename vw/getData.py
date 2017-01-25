#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
from numpy import array_split


con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb');



with con, open('data.txt','w') as f:

    print "Getting data"
    cur = con.cursor(mdb.cursors.DictCursor)

    # for Awadhi by kstronski only:
    cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words inner join sentences on words.sentence_id = sentences.id inner join documents on sentences.document_id = documents.id and documents.language_id = 3 and documents.user_id = 3 left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")

# for Rajasthani only:
#    cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words inner join sentences on words.sentence_id = sentences.id inner join documents on sentences.document_id = documents.id and documents.language_id = 2 left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")

    #cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")


    lastSentId = -1
    sentence = []
    labels = []
    for i in range(cur.rowcount):
        row = cur.fetchone()
        if row['word_text']:
            if row['sentence_id'] <> lastSentId:
                if lastSentId <> -1:
                    sentence_text = ' '.join([w[0]+'_'+('1' if w[1] else '0') for w in zip(sentence,labels)])+'\n'
                    f.write(sentence_text)
                    #print sentence_text
                    sentence = []
                    labels = []
                lastSentId = row['sentence_id']

            text = re.sub('\s+','',row['word_text']).replace('|','')
            if len(text) > 0:
                sentence.append(text)
                label = False
                if row['tag']:
                    label = '21' in row['tag'].split(',') or '85' in row['tag'].split(',')
                labels.append(label)



    print "Test data got"
