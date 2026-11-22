<?php
require_once __DIR__ . '/session_init.php';
require_once __DIR__ . '/oauth_config.php';
require_once __DIR__ . '/../classes/Pages.php';
require_once __DIR__ . '/ajax_handlers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $ajaxPage = $_GET['page'] ?? '';
    if (isset($ajaxHandlers[$ajaxPage])) {
        require $ajaxHandlers[$ajaxPage];
        exit;
    }
}

require_once __DIR__ . '/flash_messages.php';

$google_configured = (GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID_HERE');
$facebook_configured = (FACEBOOK_APP_ID !== 'YOUR_FACEBOOK_APP_ID_HERE');
?>
