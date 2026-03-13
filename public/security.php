<?php

/* ================================
   HIDE SERVER INFORMATION
================================ */

header_remove("X-Powered-By");


/* ================================
   SECURITY HEADERS
================================ */

header("X-Frame-Options: SAMEORIGIN"); // anti clickjacking
header("X-Content-Type-Options: nosniff"); // anti content sniffing
header("X-XSS-Protection: 1; mode=block"); // XSS protection
header("Referrer-Policy: strict-origin-when-cross-origin");


/* ================================
   CONTENT SECURITY POLICY
================================ */

header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; object-src 'none'; frame-ancestors 'self';");

/* ================================
   SESSION SECURITY
================================ */

ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// aktifkan jika website sudah HTTPS
ini_set('session.cookie_secure', 1);


/* ================================
   ADDITIONAL HARDENING
================================ */

// Disable file execution in upload folder if needed
// header("X-Download-Options: noopen");

// MIME protection
header("X-Permitted-Cross-Domain-Policies: none");

?>