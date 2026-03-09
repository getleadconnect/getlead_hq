<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

define('DB_PATH', __DIR__ . '/data/getlead_hq.db');
define('API_TOKEN', 'gl_reports_2026');
define('APP_NAME', 'Getlead HQ');
define('WEBHOOK_URL', 'https://akhilkrishna.com/getlead-ops/webhook.php');
define('WEBHOOK_TOKEN', 'glops_b48d049fb3264e5bc6f25626e4fec63052c6a04d');

if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new PDO('mysql:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec('PRAGMA journal_mode=WAL');
        $db->exec('PRAGMA foreign_keys=ON');
        $db->exec('PRAGMA busy_timeout=5000');
    }
    return $db;
}

function initDB() {
    $db = getDB();
    
    $db->exec("CREATE TABLE IF NOT EXISTS staff (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        role TEXT NOT NULL CHECK(role IN ('sales_rep','secretary','support','hr','finance','developer','tester','admin')),
        pin TEXT NOT NULL,
        mobile TEXT,
        telegram_id TEXT,
        active INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add mobile column if missing (migration)
    try { $db->exec("ALTER TABLE staff ADD COLUMN mobile TEXT"); } catch (Exception $e) {}
    
    // Chat logs table
    $db->exec("CREATE TABLE IF NOT EXISTS chat_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        session_id TEXT,
        staff_id INTEGER,
        staff_name TEXT,
        role TEXT DEFAULT 'user',
        message TEXT NOT NULL,
        channel TEXT DEFAULT 'telegram',
        message_id TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS daily_reports (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        staff_id INTEGER NOT NULL,
        report_date DATE NOT NULL,
        report_data TEXT NOT NULL,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME,
        FOREIGN KEY (staff_id) REFERENCES staff(id),
        UNIQUE(staff_id, report_date)
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        assigned_to INTEGER,
        created_by INTEGER NOT NULL,
        priority TEXT NOT NULL DEFAULT 'normal' CHECK(priority IN ('urgent','high','normal','low')),
        status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','in_progress','done','blocked')),
        due_date DATE,
        completed_at DATETIME,
        notes TEXT,
        category TEXT DEFAULT 'other' CHECK(category IN ('sales','development','support','hr','finance','operations','other')),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assigned_to) REFERENCES staff(id),
        FOREIGN KEY (created_by) REFERENCES staff(id)
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS task_comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task_id INTEGER NOT NULL,
        staff_id INTEGER NOT NULL,
        comment TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY (staff_id) REFERENCES staff(id)
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS task_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task_id INTEGER NOT NULL,
        staff_id INTEGER NOT NULL,
        action TEXT NOT NULL CHECK(action IN ('created','status_changed','assigned','commented','updated','deleted')),
        old_value TEXT,
        new_value TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY (staff_id) REFERENCES staff(id)
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS medication_log (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        date DATE NOT NULL UNIQUE,
        morning INTEGER DEFAULT 0,
        night INTEGER DEFAULT 0,
        d_rise INTEGER DEFAULT 0,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // HR Attendance & Reports
    $db->exec("CREATE TABLE IF NOT EXISTS hr_attendance (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        staff_id INTEGER NOT NULL,
        date DATE NOT NULL,
        status TEXT NOT NULL DEFAULT 'present' CHECK(status IN ('present','half_day','full_day_leave')),
        marked_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (staff_id) REFERENCES staff(id),
        FOREIGN KEY (marked_by) REFERENCES staff(id),
        UNIQUE(staff_id, date)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS hr_daily_reports (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        report_date DATE NOT NULL UNIQUE,
        total_employees INTEGER DEFAULT 0,
        present_count INTEGER DEFAULT 0,
        half_day_count INTEGER DEFAULT 0,
        full_day_leave_count INTEGER DEFAULT 0,
        interviews_scheduled INTEGER DEFAULT 0,
        interviews_completed INTEGER DEFAULT 0,
        hr_note TEXT,
        submitted_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME,
        FOREIGN KEY (submitted_by) REFERENCES staff(id)
    )");

    // Asset Management
    $db->exec("CREATE TABLE IF NOT EXISTS assets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        asset_tag TEXT NOT NULL UNIQUE,
        name TEXT NOT NULL,
        type TEXT NOT NULL,
        brand TEXT,
        model TEXT,
        serial_number TEXT,
        purchase_date DATE,
        purchase_price REAL,
        vendor TEXT,
        assigned_to INTEGER,
        status TEXT NOT NULL DEFAULT 'active',
        warranty_expiry DATE,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assigned_to) REFERENCES staff(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS asset_assignments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        asset_id INTEGER NOT NULL,
        staff_id INTEGER,
        assigned_at DATE NOT NULL,
        returned_at DATE,
        notes TEXT,
        FOREIGN KEY (asset_id) REFERENCES assets(id),
        FOREIGN KEY (staff_id) REFERENCES staff(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS asset_repairs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        asset_id INTEGER NOT NULL,
        date DATE NOT NULL,
        issue TEXT NOT NULL,
        cost REAL DEFAULT 0,
        vendor TEXT,
        status TEXT NOT NULL DEFAULT 'pending',
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (asset_id) REFERENCES assets(id)
    )");

    // Add remarks column to assets (migration)
    try { $db->exec("ALTER TABLE assets ADD COLUMN remarks TEXT"); } catch (Exception $e) {}
    // Add checkup_interval (days) and last_checkup columns
    try { $db->exec("ALTER TABLE assets ADD COLUMN checkup_interval INTEGER DEFAULT 90"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE assets ADD COLUMN last_checkup DATE"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE assets ADD COLUMN next_checkup DATE"); } catch (Exception $e) {}

    // Asset checkups table
    $db->exec("CREATE TABLE IF NOT EXISTS asset_checkups (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        asset_id INTEGER NOT NULL,
        checked_by INTEGER NOT NULL,
        checkup_date DATE NOT NULL,
        condition TEXT NOT NULL DEFAULT 'good' CHECK(condition IN ('good','fair','poor','damaged','missing')),
        remarks TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (asset_id) REFERENCES assets(id),
        FOREIGN KEY (checked_by) REFERENCES staff(id)
    )");

    // QR codes table (pre-printed labels mapped to assets)
    $db->exec("CREATE TABLE IF NOT EXISTS qr_codes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        qr_code TEXT UNIQUE NOT NULL,
        asset_id INTEGER DEFAULT NULL,
        mapped_at DATETIME DEFAULT NULL,
        mapped_by INTEGER DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (asset_id) REFERENCES assets(id),
        FOREIGN KEY (mapped_by) REFERENCES staff(id)
    )");

    // Projects table
    $db->exec("CREATE TABLE IF NOT EXISTS projects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT,
        status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','on_hold','completed','archived')),
        project_lead INTEGER,
        start_date DATE,
        target_date DATE,
        created_by INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_lead) REFERENCES staff(id),
        FOREIGN KEY (created_by) REFERENCES staff(id)
    )");

    // Add project_id to tasks (migration)
    try { $db->exec("ALTER TABLE tasks ADD COLUMN project_id INTEGER REFERENCES projects(id)"); } catch (Exception $e) {}

    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS login_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        staff_id INTEGER,
        ip_address TEXT,
        user_agent TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Seed staff
    $count = $db->query("SELECT COUNT(*) FROM staff")->fetchColumn();
    if ($count == 0) {
        $staff = [
            ['Akhil', 'admin', '1234'],
            ['Rakhi', 'secretary', '2345'],
            ['Harsha', 'sales_rep', '1001'],
            ['Nasiha', 'sales_rep', '1002'],
            ['Akshay', 'sales_rep', '1003'],
            ['Fahmida', 'sales_rep', '1004'],
            ['Nahila', 'sales_rep', '1005'],
            ['Ashita', 'sales_rep', '1006'],
            ['Rahoof', 'finance', '2001'],
            ['Athira', 'hr', '3001'],
            ['Hasna', 'developer', '4001'],
            ['Shaji', 'developer', '4002'],
            ['Hari', 'developer', '4003'],
            ['Arya T', 'tester', '5001'],
            ['Arya S', 'support', '6001'],
            ['Anjali', 'support', '6002'],
        ];
        $stmt = $db->prepare("INSERT INTO staff (name, role, pin) VALUES (:name, :role, :pin)");
        foreach ($staff as $s) {
            $stmt->execute([':name' => $s[0], ':role' => $s[1], ':pin' => password_hash($s[2], PASSWORD_DEFAULT)]);
        }
    }
    
    // Seed default settings
    $defaults = [
        'company_name' => 'Getlead Analytics',
        'app_name' => 'Getlead HQ',
        'timezone' => 'Asia/Kolkata',
        'default_priority' => 'normal',
        'auto_archive_days' => '30',
        'report_deadline' => '19:00',
        'weekend_reports' => '0',
        'session_timeout' => '86400',
        'webhook_url' => WEBHOOK_URL,
        'notify_task_created' => '1',
        'notify_task_completed' => '1',
        'notify_task_overdue' => '1',
        'notify_report_submitted' => '1',
        'notify_report_missing' => '1',
    ];
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
    foreach ($defaults as $k => $v) {
        $stmt->execute([':key' => $k, ':value' => $v]);
    }
}

initDB();

function generateCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRF()) . '">';
}

function requireLogin() {
    if (empty($_SESSION['staff_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!in_array($_SESSION['role'], ['admin', 'secretary'])) {
        header('Location: tasks.php');
        exit;
    }
}

function isLoggedIn() {
    return !empty($_SESSION['staff_id']);
}

function isAdmin() {
    return !empty($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'secretary']);
}

function roleLabel($role) {
    $labels = [
        'sales_rep' => 'Sales Rep',
        'secretary' => 'Secretary',
        'support' => 'Support',
        'hr' => 'HR',
        'finance' => 'Finance',
        'developer' => 'Developer',
        'tester' => 'Tester',
        'admin' => 'Admin',
    ];
    return $labels[$role] ?? ucfirst($role);
}

function roleEmoji($role) {
    $emojis = [
        'sales_rep' => '💼',
        'secretary' => '📋',
        'support' => '🎧',
        'hr' => '👥',
        'finance' => '💰',
        'developer' => '💻',
        'tester' => '🧪',
        'admin' => '⚡',
    ];
    return $emojis[$role] ?? '📌';
}

function getSetting($key, $default = null) {
    $db = getDB();
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->execute([':key' => $key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : $default;
}

function setSetting($key, $value) {
    $db = getDB();
    $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value, updated_at) VALUES (:key, :value, :now)");
    $stmt->execute([':key' => $key, ':value' => $value, ':now' => nowIST()]);
}

function sendWebhook($eventType, $data) {
    $url = getSetting('webhook_url', WEBHOOK_URL);
    if (!$url) return;
    
    $payload = json_encode([
        'event' => $eventType,
        'token' => WEBHOOK_TOKEN,
        'data' => $data,
        'timestamp' => date('c'),
    ]);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function nowIST() {
    return date('Y-m-d H:i:s');
}

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function nextAssetTag() {
    $db = getDB();
    $last = $db->query("SELECT asset_tag FROM assets ORDER BY id DESC LIMIT 1")->fetchColumn();
    if ($last && preg_match('/AST-(\d+)/', $last, $m)) {
        return 'AST-' . str_pad((int)$m[1] + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'AST-001';
}

function assetAge($purchaseDate) {
    if (!$purchaseDate) return '—';
    $d = new DateTime($purchaseDate);
    $now = new DateTime();
    $diff = $d->diff($now);
    $parts = [];
    if ($diff->y > 0) $parts[] = $diff->y . 'y';
    if ($diff->m > 0) $parts[] = $diff->m . 'm';
    if (empty($parts)) $parts[] = $diff->d . 'd';
    return implode(' ', $parts);
}

function getInitials($name) {
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    }
    return strtoupper(mb_substr($name, 0, 2));
}
