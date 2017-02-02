#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys

with open(sys.argv[1]) as f, open(sys.argv[2], 'w') as out:
    for line in f:
        sentence = line[:-1].split(' ')
        for i in range(len(sentence)):
            out.write('<p>%s <b>%s</b> %s</p>\n' % (' '.join(sentence[0:i]),sentence[i],' '.join(sentence[i+1:])))

