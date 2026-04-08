<?php
include 'partials/layout_start.php';

$userTasks    = array_values(getUserTasks($currentUser));
$userProjects = array_values(getUserProjects($currentUser));

$total      = count($userTasks);
$inProgress = count(array_filter($userTasks, fn($t) => $t['status'] === 'In Progress'));
$completed  = count(array_filter($userTasks, fn($t) => $t['status'] === 'Completed'));
$overdue    = count(array_filter($userTasks, fn($t) => isOverdue($t['due_date'], $t['status'])));

$activeProjects = array_filter($userProjects, fn($p) => $p['status'] === 'Active');

$upcoming = array_filter($userTasks, fn($t) => $t['status'] !== 'Completed');
usort($upcoming, fn($a,$b) => strtotime($a['due_date']) - strtotime($b['due_date']));
$upcoming = array_slice(array_values($upcoming), 0, 5);
?>

<div class="stats-grid">
    <div class="stat-card glass-card" style="--accent-color:rgba(0,229,255,0.2)">
        <div class="stat-header"><span class="stat-label">Total Tasks</span><span class="stat-icon cyan">📈</span></div>
        <div class="stat-value neon-text"><?= $total ?></div>
    </div>
    <div class="stat-card glass-card" style="--accent-color:rgba(187,134,252,0.2)">
        <div class="stat-header"><span class="stat-label">In Progress</span><span class="stat-icon purple">⏱</span></div>
        <div class="stat-value neon-text-purple"><?= $inProgress ?></div>
    </div>
    <div class="stat-card glass-card" style="--accent-color:rgba(0,229,255,0.2)">
        <div class="stat-header"><span class="stat-label">Completed</span><span class="stat-icon cyan">✅</span></div>
        <div class="stat-value neon-text"><?= $completed ?></div>
    </div>
    <div class="stat-card glass-card" style="--accent-color:rgba(255,64,129,0.2)">
        <div class="stat-header"><span class="stat-label">Overdue</span><span class="stat-icon red">⚠</span></div>
        <div class="stat-value" style="color:#ff4081"><?= $overdue ?></div>
    </div>
</div>

<div class="glass-card section-card">
    <h3 class="section-title cyan">📁 Active Projects</h3>
    <div class="projects-list">
        <?php foreach ($activeProjects as $project): ?>
        <div class="project-row">
            <div class="project-row-header">
                <h4><?= htmlspecialchars($project['name']) ?></h4>
                <span class="muted-text">Due: <?= $project['deadline'] ?></span>
            </div>
            <div class="progress-wrap">
                <div class="progress-labels"><span>Progress</span><span><?= $project['progress'] ?>%</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:<?= $project['progress'] ?>%"></div></div>
            </div>
            <div class="project-meta">
                <span class="muted-text">👥 <?= count($project['teamMembers']) ?> members</span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($activeProjects)): ?><p class="muted-text">No active projects.</p><?php endif; ?>
    </div>
</div>

<div class="glass-card section-card">
    <h3 class="section-title purple">⏱ Upcoming Deadlines</h3>
    <div class="deadline-list">
        <?php foreach ($upcoming as $task):
            $proj = getProjectById($task['project_id']);
            $over = isOverdue($task['due_date'], $task['status']);
        ?>
        <a href="index.php?page=kanban&task=<?= $task['id'] ?>" class="deadline-item">
            <div class="deadline-row">
                <span class="task-title"><?= htmlspecialchars($task['title']) ?></span>
                <span class="<?= $over ? 'text-red' : 'muted-text' ?>"><?= $task['due_date'] ?></span>
            </div>
            <div class="deadline-meta">
                <span class="muted-text"><?= htmlspecialchars($proj['name'] ?? '') ?></span>
                <span class="priority-badge <?= priorityClass($task['priority']) ?>"><?= $task['priority'] ?></span>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($upcoming)): ?><p class="muted-text">No upcoming tasks.</p><?php endif; ?>
    </div>
</div>

<?php include 'partials/layout_end.php'; ?>
