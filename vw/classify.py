#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
from nltk import config_megam
from nltk import MaxentClassifier
from nltk import classify
from numpy import array_split


config_megam('/var/www/test/tagging/tools/db/tags/classifier/MEGAM/megam-64.opt')

algorithm = 'MEGAM'



def getClassesDict():
    f = open('hindiclasses.sorted.txt')
    classesDict = dict()
    for line in f:
        fields = line.split()
        classesDict[fields[0]] = fields[1]
    f.close()
    return classesDict

classesDict = getClassesDict();


def extractWordFeature(wordDict, sentence, i):
    wordDict['word'] = sentence[i]
    
def extractSuffixFeature(wordDict, sentence, i, l):
    wordDict['suffix'] = sentence[i][-l:]

def extractClassFeature(wordDict, sentence, i):
    wordDict['class'] = classesDict.get(sentence[i], -1)

def extractClassContextFeature(wordDict, sentence, i, left, right):
    for pos in range(i-left, i+right+1):
        if pos <> i:
            wordDict['class'+str(pos)] = -1 if pos < 0 or pos >= len(sentence) else classesDict.get(sentence[pos], -1)

def extractWordContextFeature(wordDict, sentence, i, left, right):
    for pos in range(i-left, i+right+1):
        if pos <> i:
            wordDict['word'+str(pos)] = -1 if pos < 0 or pos >= len(sentence) else sentence[pos]

def extractCvbEndingFeature(wordDict, sentence, i):
    wordDict['cvbEnding'] = sentence[i][-1] == 'ī' or sentence[i][-1] == 'i' or sentence[i][-1] == 'a'

def extractFirstOrLastFeature(wordDict, sentence, i):
    wordDict['firstOrLast'] = i == 0 or i == len(sentence)-1

def sentenceToDictList(sentence, config):
    dictList = []
    for i in range(len(sentence)):
        word = sentence[i]

        wordDict = dict()        
        if config['word']:
            extractWordFeature(wordDict, sentence, i)
        if config['suffix']:
            extractSuffixFeature(wordDict, sentence, i, config['suffix'])
        if config['class']:
            extractClassFeature(wordDict, sentence, i)
        if config['classContext']:
            extractClassContextFeature(wordDict, sentence, i, config['classContext'], config['classContext'])
        if config['wordContext']:
            extractWordContextFeature(wordDict, sentence, i, config['wordContext'], config['wordContext'])
        if config['cvbEnding']:
            extractCvbEndingFeature(wordDict, sentence, i)
        if config['firstOrLast']:
            extractFirstOrLastFeature(wordDict, sentence, i)       
        dictList.append(wordDict)
    return dictList

def trainClassifier(data, config):
    words = []
    labels = []
    wordsSet = set()
    for sentenceDataList in data:
        for sentenceData in sentenceDataList:
            wordsSet |= set(sentenceData[0])
            words += sentenceToDictList(sentenceData[0], config)
            labels += sentenceData[1]
    classifier = MaxentClassifier.train(zip(words,labels), algorithm, trace=0, max_iter=1000)
    return (classifier, wordsSet)

def classifyAndTest(classifierTuple, data, config):
    classifier = classifierTuple[0]
    wordsSet = classifierTuple[1]

    results = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}
    resultsUnknown = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}
    for sentenceData in data:
        wordsDictList = sentenceToDictList(sentenceData[0], config)
        labelsList = sentenceData[1]
        if len(wordsDictList) <> len(labelsList) :
            raise Exception("There are more words than labels in test data")
        for i in range(len(wordsDictList)):
            prediction = classifier.classify(wordsDictList[i])
            #if prediction:
            #   print classifier.explain(wordsDictList[i])
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
                    #print "Uncaught CVB: " + sentenceData[0][i] 
                    #print "in sentence: " +' '.join(sentenceData[0])
                else:            
                    results['right'] += 1
            if sentenceData[0][i] not in wordsSet:
                resultsUnknown['total'] += 1
                if prediction:
                    if expected:
                        resultsUnknown['TP'] += 1
                        resultsUnknown['right'] += 1
                    else:
                        resultsUnknown['FP'] += 1
                        resultsUnknown['wrong'] += 1
                else:
                    if expected:
                        resultsUnknown['FN'] += 1
                        resultsUnknown['wrong'] += 1
                        #print "Uncaught CVB: " + sentenceData[0][i] 
                        #print "in sentence: " +' '.join(sentenceData[0])
                    else:            
                        resultsUnknown['right'] += 1
            
            
    return (results, resultsUnknown)

def updateStats(stats, statsUnknown, resultsTuple):
    stats['TP'] += resultsTuple[0]['TP']
    stats['FP'] += resultsTuple[0]['FP']
    stats['FN'] += resultsTuple[0]['FN']
    stats['right'] += resultsTuple[0]['right']
    stats['wrong'] += resultsTuple[0]['wrong']
    stats['total'] += resultsTuple[0]['total']

    statsUnknown['TP'] += resultsTuple[1]['TP']
    statsUnknown['FP'] += resultsTuple[1]['FP']
    statsUnknown['FN'] += resultsTuple[1]['FN']
    statsUnknown['right'] += resultsTuple[1]['right']
    statsUnknown['wrong'] += resultsTuple[1]['wrong']
    statsUnknown['total'] += resultsTuple[1]['total']

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
    return (stat,0.0) if total == 0 else (stat,float(100*stat)/total)
    
def run(config):

    con = mdb.connect('localhost', 'webuser', 'tialof', 'taggingdb');

    with con:

        print "Getting data"
        cur = con.cursor(mdb.cursors.DictCursor)
        
        # for Awadhi only:
        #cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words inner join sentences on words.sentence_id = sentences.id inner join documents on sentences.document_id = documents.id and documents.language_id = 3 left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")

	# for Rajasthani only:
        cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words inner join sentences on words.sentence_id = sentences.id inner join documents on sentences.document_id = documents.id and documents.language_id = 2 left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")

        #cur.execute("select words.sentence_id, words.id, (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text, group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from words left join word_annotations on words.id = word_annotations.word_id left join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id group by word_id order by sentence_id, words.position")


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
                               


        print "Test data got"

    stats = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}
    statsUnknown = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}

    splitData = array_split(corpusData, 10)
    for i in range(len(splitData)):
        print "Training classifier %d" % i
        classifier = trainClassifier(splitData[:i] + splitData[(i + 1):], config)
        print "Testing classifier %d" % i
        resultsTuple = classifyAndTest(classifier, splitData[i], config)
        updateStats(stats, statsUnknown, resultsTuple)


    return (stats, statsUnknown)


# Main program


#configs = [{'word':1, 'suffix':4, 'class':1,'classContext':0, 'wordContext':0, 'cvbEnding':0, 'firstOrLast':0}]

configs = [{'word':word, 'suffix':suffix, 'class':cl,'classContext':classContext, 'wordContext':wordContext, 'cvbEnding':cvbEnding, 'firstOrLast':firstOrLast} for word in range(2) for suffix in range(5) for cl in range(2) for classContext in range(3) for wordContext in range(3) for cvbEnding in range(2) for firstOrLast in range(2)]

#configs = [{'word':word, 'suffix':suffix, 'class':cl,'classContext':classContext, 'wordContext':wordContext, 'cvbEnding':cvbEnding, 'firstOrLast':firstOrLast} for word in range(1,2) for suffix in range(4,5) for cl in range(2) for classContext in range(1) for wordContext in range(1) for cvbEnding in range(1) for firstOrLast in range(1)]


maxPrecision = [(None, None, None), None]
maxRecall = [(None, None, None), None]
maxFscore = [(None, None, None), None]

count = 0
for config in configs:
    statsTuple = run(config)
    stats = statsTuple[0]
    prf = getPRFstats(stats['TP'], stats['FP'], stats['FN'])
    if prf[0] > maxPrecision[0][0]:
        maxPrecision[0] = prf
        maxPrecision[1] = config
    if prf[1] > maxRecall[0][1]:
        maxRecall[0] = prf
        maxRecall[1] = config
    if prf[2] > maxFscore[0][2]:
        maxFscore[0] = prf
        maxFscore[1] = config
    count +=1
    print "Done config number %d of %d" % (count, len(configs))
        

print "Max precision:"
print renderPRF(maxPrecision[0])
print "For config:"
print  maxPrecision[1]

print "Max recall:"
print renderPRF(maxRecall[0])
print "For config:"
print  maxRecall[1]

print "Max fscore:"
print renderPRF(maxFscore[0])
print "For config:"
print  maxFscore[1]

#print "Total stats"
#print stats
#print "Total words: %d" % stats['total']
#print "wrong: %d (%.4f%%)" % getPercentage(stats['wrong'], stats['total'])
#print "right: %d (%.4f%%)" % getPercentage(stats['right'], stats['total'])

#prf = getPRFstats(stats['TP'], stats['FP'], stats['FN'])
#print renderPRF(prf)

#print
#print
#print "Stats on unknown words"
#print statsUnknown
#print "Total words: %d" % statsUnknown['total']
#print "wrong: %d (%.4f%%)" % getPercentage(statsUnknown['wrong'], statsUnknown['total'])
#print "right: %d (%.4f%%)" % getPercentage(statsUnknown['right'], statsUnknown['total'])
#print
#prf = getPRFstats(statsUnknown['TP'], statsUnknown['FP'], statsUnknown['FN'])
#print renderPRF(prf)


