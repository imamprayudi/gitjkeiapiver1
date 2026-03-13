<?php

/* ================================
   HIDE SERVER INFORMATION
================================ */

header_remove("X-Powered-By");


/* ================================
   SECURITY HEADERS
================================ */

header("X-Frame-Options: SAMEORIGIN"); // Anti Clickjacking
header("X-Content-Type-Options: nosniff"); // Prevent MIME sniffing
header("X-XSS-Protection: 1; mode=block"); // XSS protection
header("Referrer-Policy: strict-origin-when-cross-origin");


/* ================================
   CONTENT SECURITY POLICY
================================ */

header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; object-src 'none'; frame-ancestors 'self';");


/* ================================
   HTTPS SECURITY (HSTS)
================================ */

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
}


/* ================================
   SESSION SECURITY
================================ */

ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// hanya aktif jika HTTPS
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}


/* ================================
   ADDITIONAL HARDENING
================================ */

// MIME policy
header("X-Permitted-Cross-Domain-Policies: none");

// Optional download protection
// header("X-Download-Options: noopen");

?>