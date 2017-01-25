#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys,re

text_pattern = re.compile(r'<ref>(.*?)<\/ref>\s*<dev>(.*?) \|<\/dev>')

with open(sys.argv[1]) as xml, open(sys.argv[2],'w') as contexts, open(sys.argv[3],'w') as text:
    for line in xml:
        match = text_pattern.search(line)
        if match:
            verse = match.group(1)
            sentence_text = match.group(2)
            
            sentence = sentence_text.split(' ')
            for i in range(len(sentence)):
                contexts.write('<p><i>%s</i>&nbsp;&nbsp;%s <b>%s</b> %s</p>\n' % (verse,' '.join(sentence[0:i]),sentence[i],' '.join(sentence[i+1:])))
            
            text.write(sentence_text+'\n')

        else:
            sys.stderr.write('Non matching line: '+line)
