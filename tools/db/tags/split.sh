#!/bin/sh

file=$1
name=$2

rm -rf $name
mkdir $name
split --numeric-suffixes=1 --additional-suffix=".txt" $file $name/part
