<?php

// Fix for Vercel's serverless environment
if (!isset($_SERVER['APP_PUBLIC_PATH'])) {
    $_SERVER['APP_PUBLIC_PATH'] = dirname(__DIR__) . '/public';
}

// Handle the request through Laravel's public/index.php
$app_path = __DIR__ . '/../public/index.php';

if (!file_exists($app_path)) {
    http_response_code(500);
    die('Laravel public/index.php not found at: ' . $app_path);
}

require $app_path;
