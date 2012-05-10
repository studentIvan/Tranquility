<?php
require_once dirname(__FILE__) . '/../system/__init__.php';
$pdo = Database::getInstance();

try {
    $pdo->query("TRUNCATE TABLE referrers");
}
catch (Exception $e) {
}

try {
    $pdo->query("TRUNCATE TABLE sessions");
}
catch (Exception $e) {
}

try {
    $pdo->query("TRUNCATE TABLE captcha");
}
catch (Exception $e) {
}


