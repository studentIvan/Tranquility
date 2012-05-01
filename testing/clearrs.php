<?php
require_once dirname(__FILE__) . '/../system/__init__.php';
$pdo = Database::getInstance();
$pdo->query("TRUNCATE TABLE referrers");
$pdo->query("TRUNCATE TABLE sessions");