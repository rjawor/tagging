#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, xlsxwriter
import MySQLdb as mdb



workbook = xlsxwriter.Workbook(sys.argv[1])
worksheet = workbook.add_worksheet()

con = mdb.connect('localhost', 'webuser', 'tialof', 'testtaggingdb')

with con:
    cur = con.cursor(mdb.cursors.DictCursor)
    
    cur.execute("select * from sentences inner join words on words.sentence_id = sentences.id and document_id = 2 left join word_annotations on word_annotations.word_id = words.id order by sentences.position, words.position")

    for i in range(cur.rowcount):        
        row = cur.fetchone()
    	if row['word_text']:
            worksheet.write(i, 0, row['text'].decode('utf-8'))

workbook.close()


