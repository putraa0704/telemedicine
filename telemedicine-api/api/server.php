<?php

$vercelPath = $_GET['__vc_path'] ?? null;

if (is_string($vercelPath)) {
    $normalizedPath = '/' . ltrim($vercelPath, '/');
    
    $queryParams = $_GET;
    unset($queryParams['__vc_path']);
    $queryString = http_build_query($queryParams);

    $_SERVER['REQUEST_URI'] = $normalizedPath . ($queryString !== '' ? '?' . $queryString : '');
    $_SERVER['PATH_INFO'] = $normalizedPath;
}

require __DIR__ . '/../public/index.php';