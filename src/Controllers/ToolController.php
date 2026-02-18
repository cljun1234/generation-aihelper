<?php
require_once __DIR__ . '/../Services/AIService.php';
require_once __DIR__ . '/../Helpers/CSRF.php';

class ToolController {
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
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.name as category_name
            FROM ai_tools t
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = ?
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $tools = $stmt->fetchAll();

        require_once __DIR__ . '/../../views/tools/index.php';
    }

    public function create() {
        $this->ensureAuth();
        // Get categories for dropdown
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $categories = $stmt->fetchAll();

        // Get KBs for selection
        $stmtKB = $this->pdo->prepare("SELECT * FROM knowledge_bases WHERE user_id = ?");
        $stmtKB->execute([$_SESSION['user_id']]);
        $knowledge_bases = $stmtKB->fetchAll();

        require_once __DIR__ . '/../../views/tools/editor.php';
    }

    public function store() {
        $this->ensureAuth();
        CSRF::check();

        $name = $_POST['name'];
        $description = $_POST['description'];
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        // Ensure unique slug
        $stmtSlug = $this->pdo->prepare("SELECT id FROM ai_tools WHERE slug = ?");
        $stmtSlug->execute([$slug]);
        if ($stmtSlug->fetch()) {
            $slug .= '-' . time();
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Create Tool
            $stmt = $this->pdo->prepare("
                INSERT INTO ai_tools (user_id, category_id, name, description, slug, status)
                VALUES (?, ?, ?, ?, ?, 'draft')
            ");
            $stmt->execute([$_SESSION['user_id'], $category_id, $name, $description, $slug]);
            $toolId = $this->pdo->lastInsertId();

            // 2. Save Fields
            $fields_json = $_POST['fields_json']; // Expecting JSON string from frontend
            $fields = json_decode($fields_json, true);
            if ($fields) {
                $stmtField = $this->pdo->prepare("
                    INSERT INTO tool_fields (tool_id, label, variable_name, type, options, placeholder, required, field_order)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                foreach ($fields as $index => $field) {
                    $stmtField->execute([
                        $toolId,
                        $field['label'],
                        $field['variable_name'],
                        $field['type'],
                        json_encode($field['options'] ?? []),
                        $field['placeholder'] ?? '',
                        isset($field['required']) && $field['required'] ? 1 : 0,
                        $index
                    ]);
                }
            }

            // 3. Save Prompt Config
            $system_message = $_POST['system_message'];
            $user_message_template = $_POST['user_message_template'];
            $model = $_POST['model'] ?? 'gpt-3.5-turbo';
            $provider = $_POST['provider'] ?? 'openai';

            $stmtPrompt = $this->pdo->prepare("
                INSERT INTO tool_prompts (tool_id, system_message, user_message_template, provider, model)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmtPrompt->execute([$toolId, $system_message, $user_message_template, $provider, $model]);

            // 4. Link Knowledge Bases
            if (isset($_POST['kb_ids']) && is_array($_POST['kb_ids'])) {
                $stmtKB = $this->pdo->prepare("INSERT INTO tool_knowledge_bases (tool_id, kb_id) VALUES (?, ?)");
                foreach ($_POST['kb_ids'] as $kb_id) {
                    $stmtKB->execute([$toolId, $kb_id]);
                }
            }

            $this->pdo->commit();
            header("Location: /editor/$toolId"); // Redirect to edit mode
            exit;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error saving tool: " . $e->getMessage());
        }
    }

    public function edit($id) {
        $this->ensureAuth();

        // Fetch Tool
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $tool = $stmt->fetch();

        if (!$tool) {
            die("Tool not found.");
        }

        // Fetch Categories
        $stmtCat = $this->pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
        $stmtCat->execute([$_SESSION['user_id']]);
        $categories = $stmtCat->fetchAll();

        // Fetch Fields
        $stmtFields = $this->pdo->prepare("SELECT * FROM tool_fields WHERE tool_id = ? ORDER BY field_order ASC");
        $stmtFields->execute([$id]);
        $fields = $stmtFields->fetchAll();
        // Decode options
        foreach ($fields as &$f) {
            $f['options'] = json_decode($f['options'], true);
        }

        // Fetch Prompt
        $stmtPrompt = $this->pdo->prepare("SELECT * FROM tool_prompts WHERE tool_id = ?");
        $stmtPrompt->execute([$id]);
        $prompt = $stmtPrompt->fetch();

        // Fetch Knowledge Bases (All available + Selected)
        $stmtAllKB = $this->pdo->prepare("SELECT * FROM knowledge_bases WHERE user_id = ?");
        $stmtAllKB->execute([$_SESSION['user_id']]);
        $knowledge_bases = $stmtAllKB->fetchAll();

        $stmtLinkedKB = $this->pdo->prepare("SELECT kb_id FROM tool_knowledge_bases WHERE tool_id = ?");
        $stmtLinkedKB->execute([$id]);
        $linked_kb_ids = $stmtLinkedKB->fetchAll(PDO::FETCH_COLUMN);

        require_once __DIR__ . '/../../views/tools/editor.php';
    }

    public function update($id) {
        $this->ensureAuth();
        CSRF::check();

        $name = $_POST['name'];
        $description = $_POST['description'];
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $status = $_POST['status'] ?? 'draft';

        try {
            $this->pdo->beginTransaction();

            // Verify Ownership
            $stmtCheck = $this->pdo->prepare("SELECT id FROM ai_tools WHERE id = ? AND user_id = ?");
            $stmtCheck->execute([$id, $_SESSION['user_id']]);
            if (!$stmtCheck->fetch()) die("Access Denied");

            // 1. Update Tool
            $stmt = $this->pdo->prepare("
                UPDATE ai_tools
                SET category_id = ?, name = ?, description = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$category_id, $name, $description, $status, $id]);

            // 2. Update Fields (Replace all)
            $stmtDel = $this->pdo->prepare("DELETE FROM tool_fields WHERE tool_id = ?");
            $stmtDel->execute([$id]);

            $fields_json = $_POST['fields_json'];
            $fields = json_decode($fields_json, true);
            if ($fields) {
                $stmtField = $this->pdo->prepare("
                    INSERT INTO tool_fields (tool_id, label, variable_name, type, options, placeholder, required, field_order)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                foreach ($fields as $index => $field) {
                    $stmtField->execute([
                        $id,
                        $field['label'],
                        $field['variable_name'],
                        $field['type'],
                        json_encode($field['options'] ?? []),
                        $field['placeholder'] ?? '',
                        isset($field['required']) && $field['required'] ? 1 : 0,
                        $index
                    ]);
                }
            }

            // 3. Update Prompt
            $system_message = $_POST['system_message'];
            $user_message_template = $_POST['user_message_template'];
            $model = $_POST['model'];
            $provider = $_POST['provider'] ?? 'openai';

            // Check if prompt exists, if not insert
            $stmtCheckPrompt = $this->pdo->prepare("SELECT id FROM tool_prompts WHERE tool_id = ?");
            $stmtCheckPrompt->execute([$id]);
            if ($stmtCheckPrompt->fetch()) {
                $stmtUpdatePrompt = $this->pdo->prepare("
                    UPDATE tool_prompts
                    SET system_message = ?, user_message_template = ?, provider = ?, model = ?
                    WHERE tool_id = ?
                ");
                $stmtUpdatePrompt->execute([$system_message, $user_message_template, $provider, $model, $id]);
            } else {
                $stmtInsertPrompt = $this->pdo->prepare("
                    INSERT INTO tool_prompts (tool_id, system_message, user_message_template, provider, model)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtInsertPrompt->execute([$id, $system_message, $user_message_template, $provider, $model]);
            }

            // 4. Update Linked KBs
            $stmtDelKB = $this->pdo->prepare("DELETE FROM tool_knowledge_bases WHERE tool_id = ?");
            $stmtDelKB->execute([$id]);

            if (isset($_POST['kb_ids']) && is_array($_POST['kb_ids'])) {
                $stmtKB = $this->pdo->prepare("INSERT INTO tool_knowledge_bases (tool_id, kb_id) VALUES (?, ?)");
                foreach ($_POST['kb_ids'] as $kb_id) {
                    $stmtKB->execute([$id, $kb_id]);
                }
            }

            $this->pdo->commit();
            header("Location: /editor/$id");
            exit;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error updating tool: " . $e->getMessage());
        }
    }

    public function delete($id) {
        $this->ensureAuth();
        CSRF::check();
        $stmt = $this->pdo->prepare("DELETE FROM ai_tools WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        header('Location: /dashboard');
        exit;
    }
}
