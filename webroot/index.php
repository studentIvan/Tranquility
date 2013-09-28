<?php
/**
 * Tranquility revolution
 */
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
require_once '../system/__init__.php';

if (DEVELOPER_MODE)
    echo "\n<script>var det = document.getElementById('debug_execution_time'); 
    det.innerHTML = '", number_format((microtime(true) - STARTED_AT), 4), "ms (' + det.innerHTML + ')';</script>";