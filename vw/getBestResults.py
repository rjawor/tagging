#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, os

best_precision = (None, '', '')
best_recall = (None, '', '')
best_fscore = (None, '', '')

for f_name in os.listdir('result'):
    with open('result/'+f_name) as result_file:
        file_lines = result_file.readlines()
        for line in file_lines:
        	if not 'undefined' in line:
		        if line.startswith('precision:'):
		            precision = float(line[11:-1])
		        elif line.startswith('recall:'):
		            recall = float(line[8:-1])
		        elif line.startswith('f-score:'):
		            fscore = float(line[9:-1])
        
        if best_precision[0] is None or precision > best_precision[0]:
            best_precision = (precision, f_name, ''.join(file_lines))
        if best_recall[0] is None or recall > best_recall[0]:
            best_recall = (recall, f_name, ''.join(file_lines))
        if best_fscore[0] is None or fscore > best_fscore[0]:
            best_fscore = (fscore, f_name, ''.join(file_lines))


print "Best precision: %.4f in result: %s. Full result: \n%s" % best_precision
print "Best recall: %.4f in result: %s. Full result: \n%s" % best_recall
print "Best f-score: %.4f in result: %s. Full result: \n%s" % best_fscore
