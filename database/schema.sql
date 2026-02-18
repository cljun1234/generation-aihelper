-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Domains (Allowed domains for users)
CREATE TABLE IF NOT EXISTS domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    domain VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Categories for organizing tools
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- AI Tools table
CREATE TABLE IF NOT EXISTS ai_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(255) DEFAULT 'fa-robot',
    status VARCHAR(50) DEFAULT 'draft',
    slug VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Form Fields for tools
CREATE TABLE IF NOT EXISTS tool_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_id INT NOT NULL,
    label VARCHAR(255) NOT NULL,
    variable_name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'text',
    options TEXT, -- For select fields, comma separated or JSON
    placeholder VARCHAR(255),
    required BOOLEAN DEFAULT TRUE,
    field_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tool_id) REFERENCES ai_tools(id) ON DELETE CASCADE
);

-- Prompts configuration for tools
CREATE TABLE IF NOT EXISTS tool_prompts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_id INT NOT NULL UNIQUE,
    system_message TEXT,
    user_message_template TEXT,
    provider VARCHAR(50) DEFAULT 'openai',
    model VARCHAR(50) DEFAULT 'gpt-3.5-turbo',
    temperature FLOAT DEFAULT 0.7,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tool_id) REFERENCES ai_tools(id) ON DELETE CASCADE
);

-- Knowledge Bases (Text content to append)
CREATE TABLE IF NOT EXISTS knowledge_bases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) DEFAULT 'text',
    content LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Linking Tools to Knowledge Bases
CREATE TABLE IF NOT EXISTS tool_knowledge_bases (
    tool_id INT NOT NULL,
    kb_id INT NOT NULL,
    PRIMARY KEY (tool_id, kb_id),
    FOREIGN KEY (tool_id) REFERENCES ai_tools(id) ON DELETE CASCADE,
    FOREIGN KEY (kb_id) REFERENCES knowledge_bases(id) ON DELETE CASCADE
);

-- Submissions / History
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_id INT NOT NULL,
    input_data JSON,
    output_data LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tool_id) REFERENCES ai_tools(id) ON DELETE CASCADE
);
