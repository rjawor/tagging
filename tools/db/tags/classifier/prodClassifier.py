#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import re
from nltk import config_megam
from nltk import MaxentClassifier
from nltk import classify
from numpy import array_split
import os.path

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
    for sentenceData in data:
        words += sentenceToDictList(sentenceData[0], config)
        labels += sentenceData[1]
    classifier = MaxentClassifier.train(zip(words,labels), algorithm, trace=0, max_iter=1000)
    return classifier

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
    
def trainClassifierOnProdData(config):

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
                               


        print "Train data got"
	
    classifier = trainClassifier(corpusData, config)
    return classifier


# Main program

if len(sys.argv) > 1 and os.path.isfile(sys.argv[1]):
    fileName = sys.argv[1]

    config = {'word':1, 'suffix':4, 'class':1,'classContext':0, 'wordContext':0, 'cvbEnding':0, 'firstOrLast':0}

    classifier = trainClassifierOnProdData(config)
    print "Classifier ready"

    inputFile = open(fileName)
    cvbList = []
    for line in inputFile:
        sentence = line.split()
        wordDictList = sentenceToDictList(sentence, config)
        index = 0
        for wordDict in wordDictList:
            prediction = classifier.classify(wordDict)
            if prediction:
                cvbList.append((sentence[index], line, index))
            index += 1
            
    inputFile.close()


    cvbListHeadFile = open('cvbListHead.html')
    outputFile = open('cvb.html', 'w')
    for line in cvbListHeadFile:
         outputFile.write(line)
    cvbListHeadFile.close()
    
    cvbList.sort(key=lambda tup:tup[0])
    outputFile.write('<h1>Converbs list</h1>')
    outputFile.write('<h2>Number of converbs found: %d</h2>' % len(cvbList))
    
    index = 0
    for cvb in cvbList:
        outputFile.write('<p id="word'+str(index)+'" class="word">\n')
        outputFile.write(cvb[0] + ' <span href="#word'+str(index)+'" onclick="toggle_visibility(\'context'+str(index)+'\');">show/hide context</span>\n')
        outputFile.write('</p>\n')
        outputFile.write('<p id="context'+str(index)+'" class="context-hidden">\n')
        words = cvb[1].split()
        cvbIndex = cvb[2]
        leftContext = ' '.join(words[:cvbIndex])
        rightContext = ' '.join(words[cvbIndex+1:])
        outputFile.write(leftContext+' <b>'+words[cvbIndex]+'</b> '+rightContext)
        outputFile.write('</p>\n')
        index += 1

    outputFile.write('</body></html>')
    outputFile.close()
    print "Done! Output stored in cvb.html"
else:
    print "No file given"

