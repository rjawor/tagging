#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys

inputFilePath = sys.argv[1]
granulation = int(sys.argv[2])


lineCount = 0

inputFile = open(inputFilePath, 'r')

for l in inputFile:
	lineCount += 1
inputFile.close()

testFiles = [open("test/test"+str(i)+".txt", 'w') for i in range(granulation)]
trainFiles = [open("train/train"+str(i)+".txt", 'w') for i in range(granulation)]

inputFile = open(inputFilePath, 'r')
counter = 0
for line in inputFile:
	fileNum = (counter * granulation) / lineCount
	testFiles[fileNum].write(line)
	for i in range(granulation):
		if i <> fileNum:
			trainFiles[i].write(line)
		
	counter += 1
inputFile.close()

for f in testFiles:
	f.close()
for f in trainFiles:
	f.close()

