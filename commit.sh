#!/bin/bash
#
# use this script while commit
# removing garbage on tmp
#

echo "removing garbage"
rm -rf tmp/cache/*
rm -rf tmp/sessions/*
rm -rf tmp/views/*
rm -rf tmp/profiler/*
rm -rf tmp/logs/*

echo "keep several directory"
touch tmp/cache/.gitkeep
touch tmp/sessions/.gitkeep
touch tmp/views/.gitkeep
touch tmp/profiler/.gitkeep
touch tmp/logs/.gitkeep

git add -A && git commit
echo "done"
exit 0