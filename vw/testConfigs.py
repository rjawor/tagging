#!/usr/bin/python
# -*- coding: utf-8 -*-

import os

configs = [{'LEARNING_RATE':learning_rate, 'CONTEXT':context, 'SUFFIXES':suffixes, 'FL':fl,'CLASS':cl, 'CLASS_CONTEXT':cl_context,'LOSS_FUNCTION':loss_function,'ENDING':0, 'HYPHEN':0, 'NGRAM': ngram} for learning_rate in range(1,9) for context in range(1,3) for suffixes in range(2,5) for fl in range(2) for cl in range(2) for cl_context in range(4) for loss_function in ['squared', 'classic', 'hinge','logistic', 'quantile'] for ngram in range(3)]


counter = 0

for config in configs:
    counter += 1
    print "Testing %d of %d configs" % (counter,len(configs))
    command = 'make'
    for entry in config.iteritems():
        command += ' %s=%s' % entry
    os.system(command)
