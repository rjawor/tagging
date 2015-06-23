#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
import utils

con = mdb.connect('localhost', 'webuser', 'tialof', 'testtaggingdb');


with con:

    

    cur = con.cursor(mdb.cursors.DictCursor)

    choices = {}
    cur.execute("SELECT * FROM word_annotation_type_choices")    
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        choices[row['id']] = row['word_annotation_type_id']
   

    for i in range(1,len(sys.argv)):
        
        filename = sys.argv[i]
        print "Working on file {0}".format(filename)
        cur.execute("INSERT INTO documents(name,user_id,language_id) VALUES (%s,1,1)", filename)


        docId = cur.lastrowid
        print 'inserted document: %d' % docId

        tagset = utils.getTagset()
        taggedFile = open(filename,'r')
        sentPos = 0
        for line in taggedFile:
            tokens = line.split()
            if len(tokens) > 0:
                cur.execute("INSERT INTO sentences(document_id,position) VALUES(%s,%s)", (docId, sentPos))
                sentId = cur.lastrowid
                sentPos += 1
                
                wordPos = 0
                for token in tokens:
                    word = token.split("_")[0]
                    tag = token.split("_")[1]
                    cur.execute("INSERT INTO words(sentence_id,text,split,position) VALUES(%s,%s,%s,%s)", (sentId, word, 0, wordPos))
                    wordId = cur.lastrowid
                    wordPos += 1

                    if tag <> 'empty' and tag and re.match('[\,\.\?\!:;]', word) is None:
                        choiceIds = tagset[tag]
                        annotations = {}
                        for choiceStr in choiceIds:
                            choice = int(choiceStr)
                            anType = choices[choice]
                            if anType not in annotations:
                                annotations[anType] = []
                            annotations[anType].append(choice)
                        
                        for anType, choiceIds in annotations.iteritems():
                            cur.execute("INSERT INTO word_annotations(word_id,type_id) VALUES(%s,%s)", (wordId, anType))
                            annotationId = cur.lastrowid
                            for choiceId in choiceIds:
                                cur.execute("INSERT INTO word_annotation_type_choices_word_annotations(word_annotation_id,word_annotation_type_choice_id) VALUES(%s,%s)", (annotationId, choiceId))

    taggedFile.close()

