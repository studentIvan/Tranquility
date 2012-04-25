#!/bin/sh
mysqldump -u root tranquility > tranquility.sql
echo "mysqldump ok"
git add .
echo "git add ok"
git status
echo "running commit"
git commit -m "$1"
echo "git commit $1 ok"
git push