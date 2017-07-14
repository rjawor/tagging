#!/bin/bash

cat dbmodel.sql | sed 's/tagger_dbmodel/'$1'/' > $1.sql
