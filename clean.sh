#!/bin/bash
#
# use this script while commit
# removing garbage on var folder
#

echo "removing garbage files.."
rm -rf var/cache/*
rm -rf var/sessions/*
rm -rf var/views/*
rm -rf var/logs/*
rm -rf var/database/migrations/*
rm -rf var/database/seeds/*
rm -rf lang/cache

echo "keep temporary diretories.."
touch var/cache/.gitkeep
touch var/sessions/.gitkeep
touch var/views/.gitkeep
touch var/logs/.gitkeep
touch var/database/migrations/.gitkeep
touch var/database/seeds/.gitkeep
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