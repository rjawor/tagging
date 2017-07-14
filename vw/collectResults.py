#!/usr/bin/python
# -*- coding: utf-8 -*-

import os, sys
from datetime import datetime

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



# main

stats = { 'TP':0, 'FP':0, 'FN':0, 'right':0, 'wrong':0, 'total':0}
crossvalid_gran = int(sys.argv[1])

for i in range(crossvalid_gran):
    with open('test/test'+str(i)+'.txt') as t, open('prediction/prediction'+str(i)+'.txt') as p:
        test_examples = t.readlines()
        predictions = p.readlines()
        
        if len(test_examples) <> len(predictions):
            raise Exception("Test and prediction files number %d vary in size." % i)

        for j in range(len(test_examples)):
            expected = test_examples[j].startswith('1')
            prediction = predictions[j].startswith('1')

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
                    #print "Uncaught CVB: " + sentenceData[0][i] 
                    #print "in sentence: " +' '.join(sentenceData[0])
                else:            
                    stats['right'] += 1


d = datetime.now()                   
with open('result/result_'+d.strftime("%Y%m%d_%H%M%S")+'.txt', 'w') as out_file:
    out_file.write(d.strftime("%d.%m.%Y %H:%M:%S")+'\n')
    out_file.write("config: %s\n" % sys.argv[2])
    out_file.write("total words: %d, right: %d, accuracy: %.4f%%" % (stats['total'], stats['right'], getPercentage(stats['right'], stats['total'])[1]))
    out_file.write("\n")
    out_file.write(renderPRF(getPRFstats(stats['TP'], stats['FP'], stats['FN'])))

