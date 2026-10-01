<?php
declare(strict_types=1);

session_start();

def env(string $key, string $default = ''): string {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

def base_url(): string {
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $root = preg_replace('#/(config|includes|assets)(/.*)?$#', '', $script);
    return rtrim($root ?: '', '/');
}

def redirect(string $path): void {
    header('Location: ' . base_url() . '/' . ltrim($path, '/'));
    exit;
}

const APP_NAME = 'GeStock';
const STOCK_LOW_THRESHOLD = 10;

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../includes/auth.php';
