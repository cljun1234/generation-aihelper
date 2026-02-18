<?php
require_once __DIR__ . '/../Services/AIService.php';

class PublicController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function gallery() {
        // Fetch all published tools, grouped by category
        // In a real multi-tenant system, we might need to filter by the "owner" (e.g. via subdomain or query param).
        // Since the user is asking for "white labelled in GHL", likely they will embed a specific URL.
        // I will assume for now we show all published tools for ALL users (or maybe filtered by a user_id passed in URL? like /u/{user_id}/tools)
        // But the current requirement is simpler. Let's just show all published tools for now, or assume single tenant admin.
        // The DB has `user_id` on tools.
        // Let's assume single tenant for the "admin" who creates tools for "end users".

        $stmt = $this->pdo->prepare("
            SELECT t.*, c.name as category_name
            FROM ai_tools t
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE t.status = 'published'
            ORDER BY c.name, t.name
        ");
        $stmt->execute();
        $tools = $stmt->fetchAll();

        $categories = [];
        $uncategorized = [];

        foreach ($tools as $tool) {
            if ($tool['category_name']) {
                $categories[$tool['category_name']][] = $tool;
            } else {
                $uncategorized[] = $tool;
            }
        }

        // If categories is empty but we have tools, maybe put them in "All"?
        // The screenshot shows "ALL" as a filter.
        // I will pass $tools (all) and $categories (grouped) to the view.

        require_once __DIR__ . '/../../views/public/gallery.php';
    }

    public function show($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        $tool = $stmt->fetch();

        if (!$tool) {
            http_response_code(404);
            echo "Tool not found.";
            return;
        }

        $stmtFields = $this->pdo->prepare("SELECT * FROM tool_fields WHERE tool_id = ? ORDER BY field_order ASC");
        $stmtFields->execute([$tool['id']]);
        $fields = $stmtFields->fetchAll();

        foreach ($fields as &$f) {
            $f['options'] = json_decode($f['options'], true);
        }

        require_once __DIR__ . '/../../views/public/tool.php';
    }

    public function generate() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $tool_id = $_POST['tool_id'] ?? null;
        if (!$tool_id) {
            echo json_encode(['error' => 'Tool ID required']);
            return;
        }

        // Fetch tool
        $stmt = $this->pdo->prepare("SELECT * FROM ai_tools WHERE id = ?");
        $stmt->execute([$tool_id]);
        $tool = $stmt->fetch();

        if (!$tool) {
            echo json_encode(['error' => 'Tool not found']);
            return;
        }

        // Fetch Prompt Config
        $stmtPrompt = $this->pdo->prepare("SELECT * FROM tool_prompts WHERE tool_id = ?");
        $stmtPrompt->execute([$tool_id]);
        $promptConfig = $stmtPrompt->fetch();

        if (!$promptConfig) {
             echo json_encode(['error' => 'Tool configuration incomplete (missing prompt).']);
             return;
        }

        // Fetch API Key from user_settings if available
        $stmtSettings = $this->pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
        $stmtSettings->execute([$tool['user_id']]);
        $settings = $stmtSettings->fetch();

        $apiKey = null;
        if ($settings) {
            if (($promptConfig['provider'] ?? 'openai') === 'openai') {
                $apiKey = $settings['openai_key'] ?? null;
            } elseif (($promptConfig['provider'] ?? 'openai') === 'deepseek') {
                $apiKey = $settings['deepseek_key'] ?? null;
            }
        }

        // Build System Prompt
        $systemMessage = $promptConfig['system_message'];
        $userMessageTemplate = $promptConfig['user_message_template'];

        // Replace variables in User Message Template
        // Inputs are in $_POST
        $inputData = [];
        foreach ($_POST as $key => $value) {
            if ($key === 'tool_id') continue;
            $cleanValue = htmlspecialchars(strip_tags($value)); // Basic sanitization
            $inputData[$key] = $cleanValue;

            // Replace {{variable}} in templates
            $placeholder = '{{' . $key . '}}';
            $systemMessage = str_replace($placeholder, $cleanValue, $systemMessage);
            $userMessageTemplate = str_replace($placeholder, $cleanValue, $userMessageTemplate);
        }

        // Append Knowledge Base Context
        $stmtKB = $this->pdo->prepare("
            SELECT kb.content
            FROM tool_knowledge_bases tkb
            JOIN knowledge_bases kb ON tkb.kb_id = kb.id
            WHERE tkb.tool_id = ?
        ");
        $stmtKB->execute([$tool_id]);
        $kbs = $stmtKB->fetchAll(PDO::FETCH_COLUMN);

        $context = "";
        if (!empty($kbs)) {
            $context = "\n\nCONTEXT:\n" . implode("\n\n", $kbs);
        }

        // Final System Message with Context
        $finalSystemMessage = $systemMessage . $context;

        // Call AI Service
        try {
             $result = AIService::generateCompletion(
                $promptConfig['provider'] ?? 'openai',
                $promptConfig['model'],
                $finalSystemMessage,
                $userMessageTemplate,
                $apiKey
             );

             // Log Submission
             $stmtSub = $this->pdo->prepare("INSERT INTO submissions (tool_id, input_data, output_data) VALUES (?, ?, ?)");
             $stmtSub->execute([$tool_id, json_encode($inputData), $result['content'] ?? '']);

             echo json_encode($result);

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
