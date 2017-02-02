#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, re

text_pattern = re.compile(r'<ref>(.*?)<\/ref>\s*<dev>(.*?)\|<\/dev>')

printVerse = len(sys.argv) > 3 and sys.argv[3] == 'verse'


with open(sys.argv[1]) as input_file, open(sys.argv[2], 'w') as output_file:
	for line in input_file:
		match = text_pattern.search(line)
		if match:
			verse = match.group(1)
			sentence_text = match.group(2)
			in_cvb = 0
			words = []
			for word in sentence_text.split():
				if word.startswith('<cvb>'):
					word = word[5:]
					in_cvb = 1
				curr_cvb = in_cvb
				if word.endswith('</cvb>'):
					word = word[:-6]
					in_cvb = 0

				words.append(word+'_'+str(curr_cvb))
			if printVerse:
				output_file.write(verse+' ')
			output_file.write(' '.join(words)+'\n')
		else:
			sys.stderr.write('Non matching line: '+line)
