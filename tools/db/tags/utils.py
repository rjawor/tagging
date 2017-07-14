# -*- coding: utf-8 -*-

import MySQLdb as mdb
import re

path='/var/www/test/tagging/tools/db/tags/'


def getTagset():
    tagsetFile = open(path+'tagset.txt', 'r')
    h = {}
    for line in tagsetFile:
        matchObj = re.match(r'^(\w+)\s+([0-9,]+)$', line)
        if matchObj:
            tagName = matchObj.group(1)
            tagIds = set(matchObj.group(2).split(','))
            h[tagName] = tagIds
    tagsetFile.close()
    return h

def getTag(tagset, choiceIds):
    maxTag = None
    for k,v in tagset.iteritems():
        if v.issubset(choiceIds):
            if maxTag:
                if maxTag[1] < len(v):
                    maxTag = (k, len(v))
            else:
                maxTag = (k, len(v))
                
    return None if maxTag is None else maxTag[0]
