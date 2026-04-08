<?php
require_once __DIR__ . '/../config/db.php';

function dbRow($sql, $types = '', ...$params) {
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function dbRows($sql, $types = '', ...$params) {
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function dbRun($sql, $types = '', ...$params) {
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($conn) ?: mysqli_affected_rows($conn);
}

function getUserById($id) {
    return dbRow('SELECT * FROM users WHERE id = ?', 'i', $id);
}

function getAllUsers() {
    return dbRows('SELECT * FROM users ORDER BY name');
}

function getUserByEmail($email) {
    return dbRow('SELECT * FROM users WHERE email = ?', 's', $email);
}

function getProjectById($id) {
    $project = dbRow('SELECT * FROM projects WHERE id = ?', 'i', $id);
    if ($project) $project['teamMembers'] = getProjectMemberIds($id);
    return $project;
}

function getAllProjects() {
    $projects = dbRows('SELECT * FROM projects ORDER BY created_at DESC');
    foreach ($projects as &$p) $p['teamMembers'] = getProjectMemberIds($p['id']);
    return $projects;
}

function getProjectMemberIds($projectId) {
    $rows = dbRows('SELECT user_id FROM project_members WHERE project_id = ?', 'i', $projectId);
    return array_column($rows, 'user_id');
}

function getProjectMembers($projectId) {
    return dbRows('
        SELECT u.* FROM users u
        JOIN project_members pm ON pm.user_id = u.id
        WHERE pm.project_id = ? ORDER BY u.name
    ', 'i', $projectId);
}

function getUserProjects($user) {
    if ($user['role'] === 'Client') {
        $projects = dbRows('
            SELECT p.* FROM projects p
            JOIN project_members pm ON pm.project_id = p.id
            WHERE pm.user_id = ? ORDER BY p.created_at DESC
        ', 'i', $user['id']);
    } else {
        $projects = getAllProjects();
    }
    foreach ($projects as &$p) $p['teamMembers'] = getProjectMemberIds($p['id']);
    return $projects;
}

function getTaskById($id) {
    $task = dbRow('SELECT * FROM tasks WHERE id = ?', 'i', $id);
    if ($task) $task['comments'] = getTaskComments($id);
    return $task;
}

function getTasksByProject($projectId) {
    return dbRows('SELECT * FROM tasks WHERE project_id = ?', 'i', $projectId);
}

function getUserTasks($user) {
    // Admin & Project Manager see all tasks
    if (in_array($user['role'], ['Admin', 'Project Manager']))
        return dbRows('SELECT * FROM tasks ORDER BY due_date ASC');

    // Client sees only tasks from projects they are a member of
    if ($user['role'] === 'Client')
        return dbRows('
            SELECT DISTINCT t.* FROM tasks t
            JOIN project_members pm ON pm.project_id = t.project_id
            WHERE pm.user_id = ?
            ORDER BY t.due_date ASC
        ', 'i', $user['id']);

    // Developer, Designer — see all tasks (can view + update status)
    return dbRows('SELECT * FROM tasks ORDER BY due_date ASC');
}

function getTaskComments($taskId) {
    return dbRows('
        SELECT c.*, u.name AS userName, u.avatar
        FROM comments c JOIN users u ON u.id = c.user_id
        WHERE c.task_id = ? ORDER BY c.created_at ASC
    ', 'i', $taskId);
}

function getUserNotifications($userId) {
    return dbRows('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20', 'i', $userId);
}

function unreadCount($userId) {
    $row = dbRow('SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0', 'i', $userId);
    return $row['cnt'] ?? 0;
}

function isOverdue($dueDate, $status) {
    return strtotime($dueDate) < time() && $status !== 'Completed';
}

function priorityClass($priority) {
    return match($priority) {
        'Critical' => 'priority-critical',
        'High'     => 'priority-high',
        'Medium'   => 'priority-medium',
        default    => 'priority-low',
    };
}

function statusClass($status) {
    return match($status) {
        'Active'    => 'status-active',
        'On Hold'   => 'status-hold',
        'Completed' => 'status-completed',
        default     => '',
    };
}
