#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, re

def getClassesDict():
    f = open('hindiclasses.sorted.txt')
    classesDict = dict()
    for line in f:
        fields = line.split()
        classesDict[fields[0]] = fields[1]
    f.close()
    return classesDict


def renderExample(example):
    label = example[0]
    features = example[1]

    result = label + ' | '
    for feature_name in features:
        result += feature_name
        feature_value = features[feature_name]
        if feature_value is not None:
            result += ':'+str(feature_value)
        result += ' '
    return result

def addSuffixFeature(features, word_text, suffix_length):
    features['s'+str(suffix_length)+'+'+word_text[-suffix_length:]] = None

def addContextFeatures(features, sentence, pos, window):
    for i in range(pos-window, pos+window+1):
        if i <> 0:
            if i in range(len(sentence)):
                features['c'+str(i-pos)+'+'+sentence[i]] = None
            else:
                features['c'+str(i-pos)+'+'] = None

def addClassContextFeatures(features, sentence, pos, window):
    for i in range(pos-window, pos+window+1):
        if i <> 0:
            if i in range(len(sentence)):
                features['v'+str(i-pos)+'+'+str(classesDict.get(sentence[i],-1))] = None
            else:
                features['v'+str(i-pos)+'+'] = None


def addNgramFeatures(features, word_text, ngram_length):
    for i in range(len(word_text) - ngram_length + 1):
        key = 'n'+str(ngram_length)+'+'+word_text[i:i+ngram_length]
        #print "%d-ngram of %s: %s" % (i, word_text, key)

        if key in features:
            features[key] = features[key] + 1
        else:
            features[key] = 1


# main

config_str = sys.argv[3]

config = dict()
for e in config_str.split(','):
    parameter, value = e.split('=')
    if parameter == 'loss_function':
        config[parameter] = value
    else:
        config[parameter] = int(value)

classesDict = getClassesDict();
print 'Classes dictionary got'

labels_present = 1
if len(sys.argv) > 4 and sys.argv[4] == 'no-labels':
    labels_present = 0



with open(sys.argv[1]) as in_file, open(sys.argv[2],'w') as out_file:
    for line in in_file:
        sentence = line[:-1].split(' ')
        for i in range(len(sentence)):
            word = sentence[i]

            if labels_present:
                word_text = word[:-2]
                label = '1' if word[-1] == '1' else '-1'
            else:
                word_text = word
                label = '-1'

            features = dict()
            features['t+'+word_text] = None

            word_length = len(word_text)
            vowel_count = len(re.findall(r'[aąeęioóuy]', word_text))
            if word_length > 0:
                vowel_rate = vowel_count / float(word_length)
            else:
                vowel_rate = 0

            #features['word_length'] = word_length
            #features['vowel_count'] = vowel_count
            #features['vowel_rate'] = vowel_rate

            for i in range(1,config['ngram']+1):
                addNgramFeatures(features, word_text, i)

            for j in range(2,config['suffixes']+1):
                addSuffixFeature(features, word_text, j)

            addContextFeatures(features, sentence, i, config['context'])

            if config['fl']:
                features['firstOrLast'] = 1 if (i == 0 or i == len(sentence) -1) else 0
            if config['class']:
                features['d+'+str(classesDict.get(word_text, -1))] = None
            if config['class_context'] > 0:
                addClassContextFeatures(features, sentence, i, config['class_context'])
            if config['ending']:
                features['cvbEnding'] = 1 if (word_text[-1] == 'ī' or word_text[-1] == 'i' or word_text[-1] == 'a') else 0
            if config['hyphen']:
                features['hyphen'] = 1 if ('-' in word_text) else 0

            example = (label, features)
            out_file.write(renderExample(example)+'\n')
