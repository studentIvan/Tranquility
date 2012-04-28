<?php
require_once dirname(__FILE__) . '/../system/__init__.php';
echo Security::getDigest(123456);