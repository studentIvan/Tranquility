#!/bin/sh
php testing/clearrs.php
mysqldump -u root tranquility > solutions/installer/sql/install.sql
echo "mysqldump ok"
git add .
echo "git add ok"
git status
echo "running commit"
git commit -m "$1"
echo "git commit $1 ok"
git push -u origin dev