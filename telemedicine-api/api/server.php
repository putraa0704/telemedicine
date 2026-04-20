<?php

// Preserve the original URL path sent by Vercel rewrites.
$vercelPath = $_GET['__vc_path'] ?? null;

if (is_string($vercelPath)) {
    $normalizedPath = '/' . ltrim($vercelPath, '/');
    if ($normalizedPath === '/') {
        $normalizedPath = '/';
    }

    // Keep any existing query params except the internal rewrite marker.
    $queryParams = $_GET;
    unset($queryParams['__vc_path']);
    $queryString = http_build_query($queryParams);

    $_SERVER['REQUEST_URI'] = $normalizedPath . ($queryString !== '' ? '?' . $queryString : '');
    $_SERVER['PATH_INFO'] = $normalizedPath;
}

require __DIR__ . '/../public/index.php';
