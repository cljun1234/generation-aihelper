<?php
require_once __DIR__ . '/../Helpers/CSRF.php';

class KnowledgeBaseController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    private function ensureAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $this->ensureAuth();
        $stmt = $this->pdo->prepare("SELECT * FROM knowledge_bases WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $kbs = $stmt->fetchAll();

        // This might be an AJAX call or a full page.
        // For now, let's assume it's part of the dashboard or a separate manager.
        // Or maybe just JSON if loaded via AJAX in the editor.
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json');
            echo json_encode($kbs);
            exit;
        }

        require_once __DIR__ . '/../../views/kb/index.php';
    }

    public function store() {
        $this->ensureAuth();
        CSRF::check();

        $name = $_POST['name'];
        $content = $_POST['content'];
        $type = 'text'; // For now only text supported as per plan

        $stmt = $this->pdo->prepare("INSERT INTO knowledge_bases (user_id, name, type, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $name, $type, $content]);

        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $this->pdo->lastInsertId(), 'name' => $name]);
            exit;
        }

        header('Location: /kb'); // Redirect back to KB index
    }

    public function update($id) {
        $this->ensureAuth();
        CSRF::check();

        $name = $_POST['name'];
        $content = $_POST['content'];

        $stmt = $this->pdo->prepare("UPDATE knowledge_bases SET name = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $content, $id, $_SESSION['user_id']]);

        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('Location: /kb');
    }

    public function delete($id) {
        $this->ensureAuth();
        CSRF::check();

        $stmt = $this->pdo->prepare("DELETE FROM knowledge_bases WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);

        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('Location: /kb');
    }
}
