#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, xlsxwriter
import MySQLdb as mdb

docId = int(sys.argv[2])

workbook = xlsxwriter.Workbook(sys.argv[1])
worksheet = workbook.add_worksheet()
bold = workbook.add_format({'bold': True})
wordStyle = workbook.add_format({'bold':True,'align': 'center'})
leftStyle = workbook.add_format({'bold':True,'align': 'left'})
rightStyle = workbook.add_format({'bold':True,'align': 'right'})


worksheet.set_column(0, 0, 10)
worksheet.set_column(1, 200, 15)

sentenceAnnotations = {}

con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb')
with con:
    cur = con.cursor(mdb.cursors.DictCursor)

    wordAnnotationLevels = []
    sentenceAnnotationLevels = []
    cur.execute("select * from word_annotation_types order by position")
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        wordAnnotationLevels.append(row['name'])

    cur.execute("select * from sentence_annotation_types order by position")
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        sentenceAnnotationLevels.append(row['name'])
        
    cur.execute("select sentences.id as sentenceId, sentence_annotation_types.position, sentence_annotations.text, (select count(id) from words where words.sentence_id = sentenceId) as length from sentences left join sentence_annotations on sentences.id = sentence_annotations.sentence_id left join sentence_annotation_types on sentence_annotation_types.id = sentence_annotations.type_id where sentences.document_id = %d" % docId)
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        sentenceId = int(row['sentenceId'])

        if sentenceId in sentenceAnnotations:
            sentenceAnnotations[sentenceId]["annotations"][int(row["position"])] = row["text"]
        else:
            sentenceAnnotations[sentenceId] = {'length':int(row["length"])}
            if row["position"] is not None:
                sentenceAnnotations[sentenceId]["annotations"] = {int(row["position"]):row["text"]}

def getSentenceOffset(sentenceIndex):
    # the height of the sentence is: bracket row, words row, empty row and all annotation levels
    return sentenceIndex * (3+len(wordAnnotationLevels)+len(sentenceAnnotationLevels)) 


def fillAnnotationLevels(sentenceIndex, sentenceId):
    sentenceOffset = getSentenceOffset(sentenceIndex)
    sentenceLength = sentenceAnnotations[sentenceId]['length']
    for level in enumerate(wordAnnotationLevels):
        worksheet.write(level[0]+sentenceOffset+2, 0, level[1], bold)
    for level in enumerate(sentenceAnnotationLevels):
        sheetRow = level[0]+sentenceOffset+2+len(wordAnnotationLevels)
        worksheet.write(sheetRow, 0, level[1], bold)
        annotationData = ''
        if 'annotations' in sentenceAnnotations[sentenceId]:
            if level[0] in sentenceAnnotations[sentenceId]['annotations']:
                annotationData = sentenceAnnotations[sentenceId]['annotations'][level[0]]
        worksheet.merge_range(sheetRow, 1, sheetRow, sentenceLength, annotationData.decode('utf-8'))
        
def insertWord(sentenceIndex, wordPos, wordText, isPostposition, postpositionId):
    sentenceOffset = getSentenceOffset(sentenceIndex)
    if isPostposition:
        worksheet.write(sentenceOffset, 1 + wordPos, '{--', leftStyle)
    if postpositionId is not None:
        worksheet.write(sentenceOffset, 1 + wordPos, '--}', rightStyle)
    worksheet.write(sentenceOffset+1, 1 + wordPos, wordText.decode('utf-8'), wordStyle)

def insertTag(sentenceIndex, wordPos, annotationLevel, annotationText):
    sentenceOffset = getSentenceOffset(sentenceIndex)
    worksheet.write(sentenceOffset+2+annotationLevel, 1 + wordPos, annotationText.decode('utf-8'))

con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb')
with con:
    cur = con.cursor(mdb.cursors.DictCursor)

    cur.execute("select sentences.id as sentenceId, words.id as wordId, words.position as wordPos, words.is_postposition, words.postposition_id, (case words.split when 1 then concat(words.stem, '-', words.suffix) else words.text end) as wordText, word_annotations.id as annotationId, word_annotations.text_value as annotationText, word_annotation_types.position as annotationLevel, group_concat(word_annotation_type_choices.value) as tags from sentences inner join words on words.sentence_id = sentences.id and document_id = %d left join word_annotations on word_annotations.word_id = words.id left join word_annotation_types on word_annotation_types.id = word_annotations.type_id left join word_annotation_type_choices_word_annotations on word_annotation_type_choices_word_annotations.word_annotation_id = word_annotations.id left join word_annotation_type_choices on word_annotation_type_choices.id = word_annotation_type_choices_word_annotations.word_annotation_type_choice_id group by sentenceId, wordId, annotationId, annotationLevel order by sentences.position, words.position" % docId)

    sentenceIndex = -1

    lastSentenceId = -1
    lastWordId = -1
    lastAnnotationId = -1
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        sentenceId = int(row['sentenceId'])
        wordId = int(row['wordId'])
        wordPos = int(row['wordPos'])
        wordText = row['wordText']
        isPostposition = row['is_postposition']
        postpositionId = row['postposition_id']
        annotationId = row['annotationId']
        
        
        if sentenceId <> lastSentenceId:
            sentenceIndex += 1
            fillAnnotationLevels(sentenceIndex, sentenceId)
            lastSentenceId = row['sentenceId'] 
        if wordId <> lastWordId:
            insertWord(sentenceIndex, wordPos, wordText, isPostposition, postpositionId)
            lastWordId = wordId
        if annotationId:
            annotationText = row['annotationText']
            annotationLevel = int(row['annotationLevel'])
            tags = row['tags']
            text = ''
            if row['annotationText'] is not None:
                text = row['annotationText']
            elif row['tags'] is not None:
                text = row['tags']
                       
            insertTag(sentenceIndex, wordPos, annotationLevel, text)
            


workbook.close()


