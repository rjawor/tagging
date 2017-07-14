#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, re

token_pattern = re.compile('t\+(.*?) ')


converbs_list = []
with open(sys.argv[2]) as cd:
	for line in cd:
		converbs_list.append(line[:-1])
		
converbs = set(converbs_list)

with open(sys.argv[1]) as test_file, open(sys.argv[3], 'w') as out_file:
	for line in test_file:
		word = token_pattern.search(line).group(1)
		prediction = '-1'
		if word in converbs:
			prediction = '1'
		out_file.write(prediction+'\n')
