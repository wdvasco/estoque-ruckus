<?php
/**
 * Application Bootstrap
 * Simple initialization without session management
 */

// Suppress notices and warnings for cleaner output
error_reporting(E_ERROR | E_PARSE);

// Set timezone
date_default_timezone_set('America/Sao_Paulo');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Character encoding
header('Content-Type: text/html; charset=utf-8');
?>
