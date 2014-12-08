#!/bin/bash
#
# use this script while commit
# removing garbage on tmp folder
#

echo "removing garbage files.."
rm -rf tmp/cache/*
rm -rf tmp/sessions/*
rm -rf tmp/views/*
rm -rf tmp/logs/*

echo "keep temporary diretories.."
touch tmp/cache/.gitkeep
touch tmp/sessions/.gitkeep
touch tmp/views/.gitkeep
touch tmp/logs/.gitkeep
touch config/local/.gitkeep
touch config/testing/.gitkeep
touch config/staging/.gitkeep

if [ "$1" == "commit" ] 
then
  echo "committing..."
  git add -A && git commit
fi

echo "all done :-)"
exit 0