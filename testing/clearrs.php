<?php
require_once dirname(__FILE__) . '/../system/__init__.php';
$pdo = Database::getInstance();

try {
    $pdo->query("TRUNCATE TABLE referrers");
    $pdo->query("TRUNCATE TABLE visitors");
    $pdo->query("TRUNCATE TABLE sessions");
    $pdo->query("TRUNCATE TABLE captcha");
} catch (Exception $e) {
}
