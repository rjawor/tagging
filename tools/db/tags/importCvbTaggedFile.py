#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
import utils


SENTENCES_PER_DOCUMENT = 200

DOCUMENT_NAME = 'jayasiP converbs'
DOCUMENT_OWNER = 3     #kstronski
DOCUMENT_LANGUAGE = 3  #awadhi
DOCUMENT_FOLDER = 7

con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb');
with con:
    cur = con.cursor(mdb.cursors.DictCursor)

    with open(sys.argv[1], 'r') as inputFile:

        document_number = 1
        cur.execute("INSERT INTO documents(name,user_id,language_id,folder_id) VALUES ('%s',%d,%d,%d)" % (DOCUMENT_NAME+' part '+str(document_number), DOCUMENT_OWNER, DOCUMENT_LANGUAGE, DOCUMENT_FOLDER))
        currDocId = cur.lastrowid
        print 'inserted document: %d' % currDocId
        currDocSize = 0
        sentPos = 0

        for line in inputFile:
            words = [(w.split('_')[0],w.split('_')[1]=='1') for w in line.split()]
            if len(words) > 0 and True in [w[1] for w in words]:
                currDocSize +=1
                if currDocSize >= SENTENCES_PER_DOCUMENT:
                    document_number += 1
                    cur.execute("INSERT INTO documents(name,user_id,language_id,folder_id) VALUES ('%s',%d,%d,%d)" % (DOCUMENT_NAME+' part '+str(document_number), DOCUMENT_OWNER, DOCUMENT_LANGUAGE, DOCUMENT_FOLDER))
                    currDocId = cur.lastrowid
                    print 'inserted document: %d' % currDocId
                    currDocSize = 0
                    sentPos = 0

                cur.execute("INSERT INTO sentences(document_id,position) VALUES(%d,%d)" % (currDocId, sentPos))
                sentId = cur.lastrowid
                sentPos += 1

                wordPos = 0
                for word in words:
                    cur.execute("INSERT INTO words(sentence_id,text,split,position) VALUES(%d,'%s',%d,%d)" % (sentId, word[0], 0, wordPos))
                    wordId = cur.lastrowid
                    wordPos += 1

                    if word[1]:
                        cur.execute("INSERT INTO word_annotations(word_id,type_id) VALUES(%d,2)" % wordId)
                        annotationId = cur.lastrowid
                        cur.execute("INSERT INTO word_annotation_type_choices_word_annotations(word_annotation_id,word_annotation_type_choice_id) VALUES(%d,21)" % annotationId)
