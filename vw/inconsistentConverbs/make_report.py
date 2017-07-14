#!/usr/bin/python
# -*- coding: utf-8 -*-


converbs_dict = dict()
non_converbs = []
with open('words.csv') as words:
	for line in words:
		fields = [value[1:-1] for value in line[:-1].split(';')]
		if '21' in fields[5]:
			converbs_dict[fields[4]] = (fields[0], fields[1])
		else:
			non_converbs.append((fields[4], fields[0], fields[1]))
		
non_converbs.append(('kaha','-1','-1'))
for non_converb in non_converbs:
	word = non_converb[0]
	sentence = non_converb[1]
	pos = non_converb[2]	
	if word in converbs_dict:
		print "Word %s annotated as converb in sentence http://rjawor.vm.wmi.amu.edu.pl/tagging/dashboard/index/%s/%s but not in sentence http://rjawor.vm.wmi.amu.edu.pl/tagging/dashboard/index/%s/%s" % (word, converbs_dict[word][0], converbs_dict[word][1], sentence, pos)
		
