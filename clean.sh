#!/bin/bash
#
# use this script while commit
# removing garbage on tmp folder
#

echo "removing garbage"
rm -rf tmp/cache/*
rm -rf tmp/sessions/*
rm -rf tmp/views/twig/*
rm -rf tmp/views/blade/*
rm -rf tmp/profiler/*
rm -rf tmp/logs/*

echo "keep several directory"
touch tmp/cache/.gitkeep
touch tmp/sessions/.gitkeep
touch tmp/views/twig/.gitkeep
touch tmp/views/blade/.gitkeep
touch tmp/profiler/.gitkeep
touch tmp/logs/.gitkeep

if [ "$1" == "commit" ] 
then
  echo "git commit.."
  git add -A && git commit
fi
echo "done"
exit 0
