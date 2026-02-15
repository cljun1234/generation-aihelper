-- AI Tools Table
CREATE TABLE IF NOT EXISTS ai_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    model_provider ENUM('openai', 'deepseek') DEFAULT 'openai',
    model_name VARCHAR(50) DEFAULT 'gpt-3.5-turbo',
    system_prompt TEXT,
    context_data LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Form Fields Table
CREATE TABLE IF NOT EXISTS form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_id INT NOT NULL,
    label VARCHAR(255) NOT NULL,
    field_name VARCHAR(255) NOT NULL,
    field_type ENUM('text', 'textarea', 'select') DEFAULT 'text',
    options TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tool_id) REFERENCES ai_tools(id) ON DELETE CASCADE
);

-- User Settings (API Keys)
CREATE TABLE IF NOT EXISTS user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    openai_key VARCHAR(255),
    deepseek_key VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
