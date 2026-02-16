<?php
require_once __DIR__ . '/../Services/AIService.php';

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
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $tools = $stmt->fetchAll();

        require_once __DIR__ . '/../../views/tools/index.php';
    }

    public function create() {
        $this->ensureAuth();
        $tool = null;
        require_once __DIR__ . '/../../views/tools/editor.php';
    }

    public function store() {
        $this->ensureAuth();
        CSRF::check();
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $model_provider = $_POST['model_provider'];
        $model_name = $_POST['model_name'];
        $system_prompt = $_POST['system_prompt'];
        $context_data = $_POST['context_data'];
        $fields_json = $_POST['fields_json'];

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO ai_tools (user_id, title, slug, model_provider, model_name, system_prompt, context_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $slug, $model_provider, $model_name, $system_prompt, $context_data]);
            $toolId = $this->pdo->lastInsertId();

            $fields = json_decode($fields_json, true);
            if ($fields) {
                $stmtField = $this->pdo->prepare("INSERT INTO form_fields (tool_id, label, field_name, field_type, options, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($fields as $index => $field) {
                    $stmtField->execute([
                        $toolId,
                        $field['label'],
                        $field['field_name'],
                        $field['field_type'],
                        json_encode($field['options'] ?? []),
                        $index
                    ]);
                }
            }

            $this->pdo->commit();
            header('Location: /tools');
            exit;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error saving tool: " . $e->getMessage());
        }
    }

    public function edit($id) {
        $this->ensureAuth();
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $tool = $stmt->fetch();

        if (!$tool) {
            die("Tool not found or access denied.");
        }

        $stmtFields = $this->pdo->prepare("SELECT * FROM form_fields WHERE tool_id = ? ORDER BY sort_order ASC");
        $stmtFields->execute([$id]);
        $fields = $stmtFields->fetchAll();

        // Decode options for easier use in view? JS expects array anyway.
        // But in PHP view, I used json_encode($tool['fields']).
        // The DB stores options as JSON string.
        // PDO fetchAll returns it as string.
        // In JS: fields = [ {options: "[\"A\"]"} ] -> wait, this is double encoded if I'm not careful.
        // Let's check edit view: json_encode($tool['fields'])
        // If field['options'] is string "[\"A\"]", then json_encode makes it string "[\"A\"]".
        // In JS: fields[index].options = "[\"A\"]".
        // My JS code: value="${Array.isArray(field.options) ? ... : (field.options || '')}"
        // If it's a JSON string, Array.isArray is false. It prints the raw JSON string.
        // I should probably decode it in PHP before passing to view, OR handle it in JS.
        // Let's decode in PHP Controller to be clean.
        foreach ($fields as &$f) {
            $f['options'] = json_decode($f['options'], true);
        }
        $tool['fields'] = $fields;

        require_once __DIR__ . '/../../views/tools/editor.php';
    }

    public function update($id) {
        $this->ensureAuth();
        CSRF::check();
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $model_provider = $_POST['model_provider'];
        $model_name = $_POST['model_name'];
        $system_prompt = $_POST['system_prompt'];
        $context_data = $_POST['context_data'];
        $fields_json = $_POST['fields_json'];

        try {
            $this->pdo->beginTransaction();

            $stmtCheck = $this->pdo->prepare("SELECT id FROM ai_tools WHERE id = ? AND user_id = ?");
            $stmtCheck->execute([$id, $_SESSION['user_id']]);
            if (!$stmtCheck->fetch()) {
                throw new Exception("Access Denied");
            }

            $stmt = $this->pdo->prepare("UPDATE ai_tools SET title = ?, slug = ?, model_provider = ?, model_name = ?, system_prompt = ?, context_data = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $model_provider, $model_name, $system_prompt, $context_data, $id]);

            $stmtDel = $this->pdo->prepare("DELETE FROM form_fields WHERE tool_id = ?");
            $stmtDel->execute([$id]);

            $fields = json_decode($fields_json, true);
            if ($fields) {
                $stmtField = $this->pdo->prepare("INSERT INTO form_fields (tool_id, label, field_name, field_type, options, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($fields as $index => $field) {
                    $stmtField->execute([
                        $id,
                        $field['label'],
                        $field['field_name'],
                        $field['field_type'],
                        json_encode($field['options'] ?? []),
                        $index
                    ]);
                }
            }

            $this->pdo->commit();
            header('Location: /tools');
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
        header('Location: /tools');
        exit;
    }

    // --- Public Methods ---

    public function show($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE slug = ?");
        $stmt->execute([$slug]);
        $tool = $stmt->fetch();

        if (!$tool) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            echo "Tool not found.";
            return;
        }

        $stmtFields = $this->pdo->prepare("SELECT * FROM form_fields WHERE tool_id = ? ORDER BY sort_order ASC");
        $stmtFields->execute([$tool['id']]);
        $fields = $stmtFields->fetchAll();

        // Decode options for view
        foreach ($fields as &$f) {
            $f['options'] = json_decode($f['options'], true);
        }

        require_once __DIR__ . '/../../views/tools/public.php';
    }

    public function process($id) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *'); // Allow embedding

        // Note: Strict CSRF check is disabled here to support embedding in iframes (e.g. GHL)
        // where third-party cookies/sessions might be blocked.
        // Since this action (Generate Text) does not modify user account state,
        // the risk is primarily resource usage (API costs), which should be handled via Rate Limiting in future.

        // Fetch tool and fields
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE id = ?");
        $stmt->execute([$id]);
        $tool = $stmt->fetch();

        if (!$tool) {
            echo json_encode(['success' => false, 'error' => 'Tool not found']);
            return;
        }

        // Fetch API Key from user_settings
        $stmtSettings = $this->pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
        $stmtSettings->execute([$tool['user_id']]);
        $settings = $stmtSettings->fetch();

        $apiKey = '';
        if ($tool['model_provider'] === 'openai') {
            $apiKey = $settings['openai_key'] ?? '';
        } elseif ($tool['model_provider'] === 'deepseek') {
            $apiKey = $settings['deepseek_key'] ?? '';
        }

        if (empty($apiKey)) {
            echo json_encode(['success' => false, 'error' => 'API Key not configured for this tool owner.']);
            return;
        }

        // Construct Prompt
        $systemPrompt = $tool['system_prompt'];
        $userInputs = "";

        // Iterate through POST data to build context or replace variables
        // We expect POST data keys to match field_names
        foreach ($_POST as $key => $value) {
            // Sanitize value
            $cleanValue = htmlspecialchars(strip_tags($value));

            // 1. Replace {variable} in system prompt
            // Note: str_replace matches all occurrences
            $placeholder = '{' . $key . '}';
            if (strpos($systemPrompt, $placeholder) !== false) {
                 $systemPrompt = str_replace($placeholder, $cleanValue, $systemPrompt);
            } else {
                // 2. If variable not in prompt, maybe append to user inputs?
                // For now, let's just replace. If the user didn't put {var} in prompt, it's ignored unless we force it.
                // But typically we should build a "User Message" from the inputs if they aren't in system prompt.
                // Let's append to a generic user message string just in case.
                if ($key !== 'tool_id') { // ignore internal fields if any
                     $userInputs .= "$key: $cleanValue\n";
                }
            }
        }

        // Append Context Data (Knowledge Base)
        if (!empty($tool['context_data'])) {
            $systemPrompt .= "\n\nCONTEXT DATA:\n" . $tool['context_data'];
        }

        // Call AI Service
        // For User Prompt, we can use the aggregated inputs or just a generic "Please proceed based on system instructions."
        // Since we replaced variables in System Prompt, the System Prompt now contains the specific request.
        // So User Prompt can be minimal or empty, but some APIs require it.
        // We'll use the leftover inputs as User Prompt.

        if (empty($userInputs)) {
            $userInputs = "Please generate the result based on the instructions.";
        }

        $result = AIService::generateCompletion(
            $tool['model_provider'],
            $tool['model_name'],
            $systemPrompt,
            $userInputs,
            $apiKey
        );

        echo json_encode($result);
    }
}
