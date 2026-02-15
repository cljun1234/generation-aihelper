<?php

class DashboardController {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Fetch configured domain (existing logic kept for compatibility)
        $stmt = $pdo->prepare("SELECT * FROM domains WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $domain = $stmt->fetch();

        require_once __DIR__ . '/../../views/pages/home.php';
    }

    public function settings() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Fetch configured domain
        $stmt = $pdo->prepare("SELECT * FROM domains WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $domain = $stmt->fetch();

        // Fetch API Keys
        $stmtSettings = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ? LIMIT 1");
        $stmtSettings->execute([$user_id]);
        $apiSettings = $stmtSettings->fetch();

        require_once __DIR__ . '/../../views/pages/settings.php';
    }

    public function saveSettings() {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = Database::getInstance();
        $user_id = $_SESSION['user_id'];

        // Save Domain
        $domain_name = $_POST['domain'] ?? '';

        $stmtCheck = $pdo->prepare("SELECT id FROM domains WHERE user_id = ?");
        $stmtCheck->execute([$user_id]);
        if ($stmtCheck->fetch()) {
            $stmt = $pdo->prepare("UPDATE domains SET domain = ? WHERE user_id = ?");
            $stmt->execute([$domain_name, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO domains (user_id, domain) VALUES (?, ?)");
            $stmt->execute([$user_id, $domain_name]);
        }

        // Save API Keys
        $openai_key = $_POST['openai_key'] ?? '';
        $deepseek_key = $_POST['deepseek_key'] ?? '';

        $stmtCheck = $pdo->prepare("SELECT id FROM user_settings WHERE user_id = ?");
        $stmtCheck->execute([$user_id]);
        if ($stmtCheck->fetch()) {
            $stmt = $pdo->prepare("UPDATE user_settings SET openai_key = ?, deepseek_key = ? WHERE user_id = ?");
            $stmt->execute([$openai_key, $deepseek_key, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, openai_key, deepseek_key) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $openai_key, $deepseek_key]);
        }

        header('Location: /settings');
    }
}
