<?php
// Start session in a central place if not already started
ini_set('session.save_path', '/tmp/php_sessions');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/AltoRouter.php';

// Load .env manually if not using a library (since composer.json/vendor might be missing in some envs)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Helpers/CSRF.php';

// Autoload Controllers
spl_autoload_register(function ($class) {
    if (strpos($class, 'Controller') !== false) {
        $file = __DIR__ . '/../src/Controllers/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

$router = new AltoRouter();

// --- Routes ---

// Auth
$router->map('GET', '/login', 'AuthController#showLogin', 'login');
$router->map('POST', '/login', 'AuthController#processLogin', 'login_post');
$router->map('GET', '/logout', 'AuthController#logout', 'logout');

// Dashboard (Admin Home)
$router->map('GET', '/', 'ToolController#index', 'home');
$router->map('GET', '/dashboard', 'ToolController#index', 'dashboard');

// Settings (Keep existing functionality if DashboardController exists, otherwise redirect)
if (file_exists(__DIR__ . '/../src/Controllers/DashboardController.php')) {
    $router->map('GET', '/settings', 'DashboardController#settings', 'settings');
    $router->map('POST', '/settings/save', 'DashboardController#saveSettings', 'save_settings');
}

// Tool Editor
$router->map('GET', '/editor', 'ToolController#create', 'tool_create');
$router->map('POST', '/editor', 'ToolController#store', 'tool_store');
$router->map('GET', '/editor/[i:id]', 'ToolController#edit', 'tool_edit');
$router->map('POST', '/editor/[i:id]', 'ToolController#update', 'tool_update');
$router->map('POST', '/tool/delete/[i:id]', 'ToolController#delete', 'tool_delete');

// Knowledge Base
$router->map('GET', '/kb', 'KnowledgeBaseController#index', 'kb_index');
$router->map('POST', '/kb', 'KnowledgeBaseController#store', 'kb_store');
$router->map('POST', '/kb/[i:id]/update', 'KnowledgeBaseController#update', 'kb_update');
$router->map('POST', '/kb/[i:id]/delete', 'KnowledgeBaseController#delete', 'kb_delete');

// Public / Embedded Views
$router->map('GET', '/gallery', 'PublicController#gallery', 'public_gallery');
$router->map('GET', '/tool/[*:slug]', 'PublicController#show', 'public_tool');
$router->map('POST', '/api/generate', 'PublicController#generate', 'api_generate');

// Match request
$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} elseif ($match) {
    list($controller, $action) = explode('#', $match['target']);
    if (class_exists($controller) && method_exists($controller, $action)) {
        $obj = new $controller();
        call_user_func_array([$obj, $action], $match['params']);
    } else {
        // Handle error: controller or method not found
        header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
        echo "Error: Controller or action not found ($controller, $action).";
    }
} else {
    // 404
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "404 Not Found";
}
