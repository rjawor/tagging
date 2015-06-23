#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
from sklearn.feature_extraction import DictVectorizer
from sklearn import linear_model
from numpy import array_split

vectorizer = DictVectorizer()


def sentenceToDictList(sentence):
    dictList = []
    for i in range(len(sentence)):
        word = sentence[i]
        wordDict = {
                       'word':word
                       #'suffix':word[-3:],
                       #'word-2':None if i-2 < 0 else sentence[i-2],
                       #'word-1':None if i-1 < 0 else sentence[i-1],
                       #'word+1':None if i+1 > len(sentence) - 1 else sentence[i+1],
                       #'word+2':None if i+2 > len(sentence) - 1 else sentence[i+2],
                   }
        dictList.append(wordDict)
    return dictList

def trainClassifier(data):
    words = []
    labels = []
    for sentenceDataList in data:
        for sentenceData in sentenceDataList:
            words += sentenceToDictList(sentenceData[0])
            labels += sentenceData[1]
    classifier = linear_model.LogisticRegression()
    classifier.fit(vectorizer.fit_transform(words), labels)
    return classifier

def classifyAndTest(classifier, data):
    results = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}
    for sentenceData in data:
        wordsDictList = sentenceToDictList(sentenceData[0])
        labelsList = sentenceData[1]
        if len(wordsDictList) <> len(labelsList) :
            raise Exception("There are more words than labels in test data")
        inputList = vectorizer.fit_transform(wordsDictList)    
        for i in range(len(inputList)):
            prediction = classifier.predict(inputList[i])
            expected = labelsList[i]
            results['total'] += 1
            if prediction:
                if expected:
                    results['TP'] += 1
                    results['right'] += 1
                else:
                    results['FP'] += 1
                    results['wrong'] += 1
            else:
                if expected:
                    results['FN'] += 1
                    results['wrong'] += 1
                else:            
                    results['right'] += 1
    return results

def updateStats(stats, results):
    stats['TP'] += results['TP']
    stats['FP'] += results['FP']
    stats['FN'] += results['FN']
    stats['right'] += results['right']
    stats['wrong'] += results['wrong']
    stats['total'] += results['total']

def getPRFstats(tp, fp, fn):
    precision = float(tp) / (tp+fp) if tp+fp else None
    recall = float(tp) / (tp+fn) if tp+fn else None
    fscore = (2*precision*recall)/(precision+recall) if (not (precision is None or recall is None)) and (precision + recall > 0) else None
    return (precision, recall, fscore)
    

def renderPRF(prf):
    result = 'precision: '
    if prf[0] is None:
        result += 'undefined\n'
    else:
        result += '%.4f\n' % prf[0]

    result += 'recall: '
    if prf[1] is None:
        result += 'undefined\n'
    else:
        result += '%.4f\n'  % prf[1]

    result += 'f-score: '
    if prf[2] is None:
        result += 'undefined'
    else:
        result += '%.4f'  % prf[2]
    
    return result

def getPercentage(stat,total):
    return (stat,float(100*stat)/total)
    
# Main program

con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb');

with con:

    print "Getting data"
    cur = con.cursor(mdb.cursors.DictCursor)
    cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")


    corpusData = []
    
    lastSentId = -1
    sentence = []
    labels = []
    for i in range(cur.rowcount):        
        row = cur.fetchone()
        if row['word_text']:
            if row['sentence_id'] <> lastSentId:
                if lastSentId <> -1:
                    corpusData.append((sentence,labels))
                    sentence = []
                    labels = []
                lastSentId = row['sentence_id']
            
            text = re.sub('\s+','',row['word_text'])
            sentence.append(text)
            label = False
            if row['tag']:
                label = '21' in row['tag'].split(',') or '85' in row['tag'].split(',')
            labels.append(label)
                           

stats = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}


for sample in corpusData:
    sentence = sample[0]
    labels = sample[1]
    guesses = []
    for i in range(len(sentence)):
        word = sentence[i]
        word = re.sub('\s*,\s*$','',word)
        guess = word.endswith("i") or word.endswith("ī") or word.endswith("ar") or word.endswith("ke") or word.endswith("ra")
        if i == 0 or i == len(sentence) - 1:
            guess = False
        guesses.append(guess)
    
    assert len(guesses) == len(labels), "lengths of guesses and labels do not match"
        
    for i in range(len(guesses)):
        prediction = guesses[i]
        expected = labels[i]
        stats['total'] += 1
        if prediction:
            if expected:
                stats['TP'] += 1
                stats['right'] += 1
            else:
                stats['FP'] += 1
                stats['wrong'] += 1
        else:
            if expected:
                stats['FN'] += 1
                stats['wrong'] += 1
            else:            
                stats['right'] += 1

print stats
print "Total words: %d" % stats['total']
print "wrong: %d (%.4f%%)" % getPercentage(stats['wrong'], stats['total'])
print "right: %d (%.4f%%)" % getPercentage(stats['right'], stats['total'])

print

prf = getPRFstats(stats['TP'], stats['FP'], stats['FN'])
print renderPRF(prf)



