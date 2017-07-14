#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, re

token_pattern = re.compile('t\+(.*?) ')


with open(sys.argv[1]) as in_file, open(sys.argv[2],'w') as out_file:
    for line in in_file:
    	if line.startswith('1'):
            word = token_pattern.search(line).group(1)
            out_file.write(word+'\n')

