<?php
session_start();

// ═══════════════════════════════════════════════════════════════════
// Front Controller — All requests routed through here
// ═══════════════════════════════════════════════════════════════════

// Detect base URL for subdirectory installations
// When public/ is the document root (production), BASE_URL should be empty.
// When running in a subdirectory (e.g. Laragon), it auto-detects the prefix.
$scriptName = $_SERVER['SCRIPT_NAME'];  // e.g. /index.php or /PortalApp/public/index.php
$baseUrl = dirname($scriptName);         // e.g. / or /PortalApp/public

// Normalize: if document root serves index.php directly, base is empty
if ($baseUrl === '/' || $baseUrl === '\\' || $baseUrl === '.') {
    $baseUrl = '';
}
define('BASE_URL', rtrim($baseUrl, '/'));

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/core/helpers.php';
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Router.php';

// Autoload models + controllers
foreach (glob(BASE_PATH . '/models/*.php') as $f) require_once $f;
foreach (glob(BASE_PATH . '/controllers/*.php') as $f) require_once $f;

// Create router and load routes
$router = new Router();
require_once BASE_PATH . '/routes.php';

// Determine the clean request path
$requestUri = $_SERVER['REQUEST_URI'];

// Strip base path prefix
if (BASE_URL && strpos($requestUri, BASE_URL) === 0) {
    $requestUri = substr($requestUri, strlen(BASE_URL));
}

// Remove query string
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}

// Ensure leading slash
if (empty($requestUri) || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// Dispatch
$router->dispatch($_SERVER['REQUEST_METHOD'], $requestUri);
