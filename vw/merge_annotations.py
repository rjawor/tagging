#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys

with open(sys.argv[1], 'r') as text_file, open(sys.argv[2], 'r') as predictions_file:
    predictions = predictions_file.readlines()
    word_index = 0
    for line in text_file:
        words = line[:-1].split(' ')
        annotated_words = []
        for word in words:
            word_text = word.split('_')[0]
            annotation = 0
            if predictions[word_index].startswith("1"):
                annotation = 1
            annotated_words.append(word_text+'_'+str(annotation))
            word_index += 1
        print ' '.join(annotated_words)
    if word_index <> len(predictions):
        sys.stderr("Size difference. word_index=%d, len(predictions)=%d" %(word_index, len(predictions)))
