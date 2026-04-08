<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/data.php';

$action = $_POST['action'] ?? '';

// ── LOGIN ─────────────────────────────────────────────────
if ($action === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !$password) {
        $_SESSION['login_error'] = 'Please fill in all fields';
    } else {
        $user = getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header('Location: index.php?page=dashboard'); exit;
        } else {
            $_SESSION['login_error'] = 'Invalid email or password';
        }
    }
}

// ── REGISTER ──────────────────────────────────────────────
if ($action === 'register') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = $_POST['role'] ?? 'Developer';
    $allowed  = ['Admin','Project Manager','Developer','Designer','Client'];
    if (!in_array($role, $allowed)) $role = 'Developer';

    if (!$name || !$email || !$password) {
        $_SESSION['reg_error'] = 'Please fill in all fields';
    } elseif (strlen($password) < 6) {
        $_SESSION['reg_error'] = 'Password must be at least 6 characters';
    } elseif (getUserByEmail($email)) {
        $_SESSION['reg_error'] = 'Email already exists';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id = dbRun('INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)', 'ssss', $name, $email, $hash, $role);
        $_SESSION['user'] = getUserById($id);
        header('Location: index.php?page=dashboard'); exit;
    }
}

// ── UPDATE TASK STATUS ────────────────────────────────────
if ($action === 'update_task_status') {
    $taskId  = (int)($_POST['task_id'] ?? 0);
    $status  = $_POST['status'] ?? '';
    $allowed = ['Backlog','To Do','In Progress','Review','Completed'];
    $user    = $_SESSION['user'];

    if ($taskId && in_array($status, $allowed)) {
        $task = getTaskById($taskId);

        // Admins & PMs can move any task
        // Developers, Designers, Clients can only move tasks assigned to them
        // Clients cannot move tasks — all other roles can move any task
        $canUpdate = $user['role'] !== 'Client';

        if ($canUpdate && $task) {
            dbRun('UPDATE tasks SET status = ? WHERE id = ?', 'si', $status, $taskId);
            dbRun('INSERT INTO notifications (user_id, message, type) VALUES (?,?,?)',
                'iss',
                $user['id'],
                'Task "' . $task['title'] . '" moved to ' . $status,
                'task'
            );
        }
    }
    header('Location: index.php?page=kanban'); exit;
}

// ── CREATE TASK ───────────────────────────────────────────
if ($action === 'create_task') {
    $user = $_SESSION['user'];
    if (in_array($user['role'], ['Admin', 'Project Manager'])) {
        dbRun('INSERT INTO tasks (title, description, status, priority, assigned_to, project_id, due_date) VALUES (?,?,?,?,?,?,?)',
            'ssssiis',
            trim($_POST['title'] ?? 'Untitled'),
            trim($_POST['description'] ?? ''),
            $_POST['status']     ?? 'To Do',
            $_POST['priority']   ?? 'Medium',
            (int)($_POST['assignedTo']  ?? 0),
            (int)($_POST['projectId']   ?? 0),
            $_POST['dueDate']    ?? date('Y-m-d')
        );
    }
    $return = $_POST['return_page'] ?? 'dashboard';
    header('Location: index.php?page=' . $return); exit;
}

// ── CREATE PROJECT ────────────────────────────────────────
if ($action === 'create_project') {
    $user = $_SESSION['user'];
    if (in_array($user['role'], ['Admin', 'Project Manager'])) {
        $projId = dbRun('INSERT INTO projects (name, description, deadline, status) VALUES (?,?,?,?)',
            'ssss',
            trim($_POST['name'] ?? 'New Project'),
            trim($_POST['description'] ?? ''),
            $_POST['deadline'] ?? date('Y-m-d'),
            'Active'
        );
        $members = $_POST['teamMembers'] ?? [];
        foreach ($members as $uid) {
            dbRun('INSERT IGNORE INTO project_members (project_id, user_id) VALUES (?,?)', 'ii', $projId, (int)$uid);
        }
    }
    header('Location: index.php?page=projects'); exit;
}

// ── ADD COMMENT ───────────────────────────────────────────
if ($action === 'add_comment') {
    $taskId = (int)($_POST['task_id'] ?? 0);
    $text   = trim($_POST['comment_text'] ?? '');
    $userId = $_SESSION['user']['id'];
    if ($taskId && $text) {
        dbRun('INSERT INTO comments (task_id, user_id, text) VALUES (?,?,?)', 'iis', $taskId, $userId, $text);
        // Notify
        $task = getTaskById($taskId);
        if ($task) {
            dbRun('INSERT INTO notifications (user_id, message, type) VALUES (?,?,?)',
                'iss', $userId, 'New comment on "' . $task['title'] . '"', 'comment');
        }
    }
    header('Location: index.php?page=kanban&task=' . $taskId); exit;
}

// ── MARK NOTIFICATION READ ────────────────────────────────
if ($action === 'mark_notification_read') {
    $notifId = (int)($_POST['notif_id'] ?? 0);
    $userId  = $_SESSION['user']['id'];
    if ($notifId) {
        dbRun('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?', 'ii', $notifId, $userId);
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=dashboard')); exit;
}
