#!/bin/sh
mysqldump -u root turbo > turbo.sql
echo "mysqldump ok"
git add .
echo "git add ok"
git commit -m "$1"
echo "git commit $1 ok"
git push