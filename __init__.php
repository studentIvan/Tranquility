<?php
/**
 * Application bootstrap
 */
Process::$context['http_host'] = $_SERVER['HTTP_HOST'];
Process::load('ULogin');
ULogin::initSession();