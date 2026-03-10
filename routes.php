<?php
// ═══════════════════════════════════════════════════════════════════
// Route Definitions
// ═══════════════════════════════════════════════════════════════════

// ── Public ──
$router->get('/login', ['AuthController', 'showLogin']);
$router->post('/login', ['AuthController', 'login']);
$router->get('/logout', ['AuthController', 'logout']);
$router->post('/logout', ['AuthController', 'logout']);

// ── Dashboard ──
$router->get('/', ['DashboardController', 'index'], ['Auth::requireLogin']);

// ── Users (Super Admin only) ──
$router->get('/users', ['UserController', 'index'], ['Auth::requireSuperAdmin']);
$router->post('/users/store', ['UserController', 'store'], ['Auth::requireSuperAdmin']);
$router->post('/users/{id}/update', ['UserController', 'update'], ['Auth::requireSuperAdmin']);
$router->post('/users/{id}/delete', ['UserController', 'destroy'], ['Auth::requireSuperAdmin']);

// ── Certificates (Super Admin only) ──
$router->get('/certificates', ['CertificateController', 'index'], ['Auth::requireSuperAdmin']);
$router->post('/certificates/store', ['CertificateController', 'store'], ['Auth::requireSuperAdmin']);
$router->post('/certificates/{id}/update', ['CertificateController', 'update'], ['Auth::requireSuperAdmin']);
$router->post('/certificates/{id}/delete', ['CertificateController', 'destroy'], ['Auth::requireSuperAdmin']);
$router->post('/certificates/reference-data', ['CertificateController', 'updateReferenceData'], ['Auth::requireSuperAdmin']);
// AJAX endpoint for dynamic reference data
$router->get('/api/certificates/reference-data/{slug}', ['CertificateController', 'getReferenceDataJson'], ['Auth::requireLogin']);

// ── Marketing (Admin/Manager+) ──
$router->get('/marketing', ['MarketingController', 'index'], ['Auth::requireAdminOrManager']);
$router->post('/marketing/store', ['MarketingController', 'store'], ['Auth::requireAdminOrManager']);
$router->post('/marketing/{id}/update', ['MarketingController', 'update'], ['Auth::requireAdminOrManager']);
$router->post('/marketing/{id}/delete', ['MarketingController', 'destroy'], ['Auth::requireAdminOrManager']);

// ── Submissions ──
$router->get('/submissions', ['SubmissionController', 'index'], ['Auth::requireLogin']);
$router->get('/submissions/new', ['SubmissionController', 'create'], ['Auth::requireLogin']);
$router->post('/submissions/store', ['SubmissionController', 'store'], ['Auth::requireLogin']);
$router->post('/submissions/{id}/update', ['SubmissionController', 'update'], ['Auth::requireLogin']);
$router->post('/submissions/{id}/delete', ['SubmissionController', 'destroy'], ['Auth::requireLogin']);

// ── Transactions ──
$router->get('/transactions', ['TransactionController', 'index'], ['Auth::requireLogin']);
$router->post('/transactions/store', ['TransactionController', 'store'], ['Auth::requireLogin']);
$router->post('/transactions/{id}/update', ['TransactionController', 'update'], ['Auth::requireLogin']);
$router->post('/transactions/{id}/delete', ['TransactionController', 'destroy'], ['Auth::requireLogin']);

// ── Fee P3SM ──
$router->get('/fee-p3sm', ['FeeP3smController', 'index'], ['Auth::requireLogin']);
$router->post('/fee-p3sm/store', ['FeeP3smController', 'store'], ['Auth::requireLogin']);
$router->post('/fee-p3sm/{id}/update', ['FeeP3smController', 'update'], ['Auth::requireLogin']);
$router->post('/fee-p3sm/{id}/delete', ['FeeP3smController', 'destroy'], ['Auth::requireLogin']);

// ── Settings ──
$router->get('/settings', ['SettingsController', 'index'], ['Auth::requireAdmin']);
$router->get('/settings/export', ['SettingsController', 'exportJson'], ['Auth::requireAdmin']);
$router->post('/settings/import', ['SettingsController', 'importJson'], ['Auth::requireAdmin']);

// ── API (AJAX endpoints for dynamic data) ──
$router->get('/api/marketing/list', ['MarketingController', 'listJson'], ['Auth::requireLogin']);
$router->get('/api/dashboard/summary', ['DashboardController', 'summaryJson'], ['Auth::requireLogin']);
$router->get('/api/dashboard/ranking', ['DashboardController', 'rankingJson'], ['Auth::requireLogin']);
$router->get('/api/dashboard/chart-data', ['DashboardController', 'chartDataJson'], ['Auth::requireLogin']);
