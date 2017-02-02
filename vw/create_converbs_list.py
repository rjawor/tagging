#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys


with open(sys.argv[1]) as pred, open(sys.argv[2]) as cont, open(sys.argv[3], 'w') as out:
	out.write('<html><head><meta charset="UTF-8"></head><body>\n')
	for prediction in pred:
		context = cont.readline()
		if prediction.startswith('1'):
			out.write(context)
		
	out.write('</body></html>\n')

