<?php

header_remove("X-Powered-By");

/* SECURITY HEADERS */
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

/* CONTENT SECURITY POLICY */
$csp = "
default-src 'self';
script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline';
style-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline';
img-src 'self' data:;
connect-src 'self' http://136.198.117.117 http://localhost https://jkei.jvckenwood.com https:;
font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;
object-src 'none';
frame-ancestors 'self';
base-uri 'self';
form-action 'self';
";

/* hapus newline/tab/spasi berlebih */
$csp = preg_replace('/\s+/', ' ', trim($csp));

header("Content-Security-Policy: $csp");

/* HTTPS SECURITY */
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
}

/* SESSION SECURITY */
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

/* ADDITIONAL HARDENING */
header("X-Permitted-Cross-Domain-Policies: none");
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), fullscreen=(self)");
?>