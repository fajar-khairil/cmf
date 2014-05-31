#!/bin/bash
#
# use this script while commit
# removing garbage on engine/tmp
#

echo "removing garbage"
rm -rf engine/tmp/cache/*
rm -rf engine/tmp/sessions/*
rm -rf engine/tmp/views/*
rm -rf engine/tmp/profiler/*
rm -rf engine/logs/profiler/*

echo "keep several directory"
touch engine/tmp/cache/.gitkeep
touch engine/tmp/sessions/.gitkeep
touch engine/tmp/views/.gitkeep
touch engine/tmp/profiler/.gitkeep
touch engine/tmp/logs/.gitkeep

git add -A && git commit
echo "done"
exit 0