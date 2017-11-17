<?php
session_start();

$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'phpmyadmin',
        'password' => '',
        'db' => 'lr'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => '604800'
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token'
    )
);
//Replace with autoload
spl_autoload_register( function($class) {
    require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';