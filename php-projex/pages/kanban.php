<?php
include 'partials/layout_start.php';

$userTasks = array_values(getUserTasks($currentUser));
$columns   = ['Backlog','To Do','In Progress','Review','Completed'];
$taskId    = isset($_GET['task']) ? (int)$_GET['task'] : null;
$selectedTask = $taskId ? getTaskById($taskId) : null;

$columnColors = [
    'Backlog'     => '#9090b0',
    'To Do'       => '#00e5ff',
    'In Progress' => '#bb86fc',
    'Review'      => '#00e5ff',
    'Completed'   => '#00ff88',
];
?>
<div class="kanban-header">
    <h2 class="page-title neon-text">Task Board</h2>
    <p class="muted-text">Click on a task to view details · Use status buttons to move tasks</p>
</div>

<div class="kanban-board">
    <?php foreach ($columns as $col):
        $colTasks = array_values(array_filter($userTasks, fn($t) => $t['status'] === $col));
        $color    = $columnColors[$col];
    ?>
    <div class="kanban-col glass-card" style="--col-color:<?= $color ?>">
        <div class="col-header" style="background:linear-gradient(to bottom,<?= $color ?>22,transparent)">
            <span class="col-title"><?= $col ?></span>
            <span class="col-count"><?= count($colTasks) ?></span>
        </div>
        <div class="col-tasks">
            <?php foreach ($colTasks as $task):
                $assignee = getUserById($task['assigned_to']);
                $proj     = getProjectById($task['project_id']);
                $over     = isOverdue($task['due_date'], $task['status']);
            ?>
            <a href="index.php?page=kanban&task=<?= $task['id'] ?>" class="task-card glass-card <?= $over?'overdue':'' ?>">
                <div class="task-card-top">
                    <h4 class="task-title"><?= htmlspecialchars($task['title']) ?></h4>
                    <?php if ($over): ?><span class="overdue-icon" title="Overdue">⚠</span><?php endif; ?>
                </div>
                <p class="task-desc muted-text"><?= htmlspecialchars(mb_substr($task['description'],0,80)) ?>...</p>
                <div class="task-footer">
                    <div class="assignee">
                        <span class="avatar-sm"><?= $assignee['avatar'] ?? '👤' ?></span>
                        <span class="muted-text"><?= htmlspecialchars($assignee['name'] ?? '') ?></span>
                    </div>
                    <span class="muted-text small">⏱ <?= $task['due_date'] ?></span>
                </div>
                <div class="task-tags">
                    <span class="tag muted-bg"><?= htmlspecialchars($proj['name'] ?? '') ?></span>
                    <span class="priority-badge <?= priorityClass($task['priority']) ?>"><?= $task['priority'] ?></span>
                </div>
                <?php
                $commentCount = count(getTaskComments($task['id']));
                if ($commentCount > 0): ?>
                <div class="muted-text small mt-2">💬 <?= $commentCount ?> comment<?= $commentCount!==1?'s':'' ?></div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
            <?php if (empty($colTasks)): ?>
            <div class="empty-col muted-text">No tasks here</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($selectedTask):
    $assignee = getUserById($selectedTask['assigned_to']);
    $proj     = getProjectById($selectedTask['project_id']);
    $over     = isOverdue($selectedTask['due_date'], $selectedTask['status']);
?>
<div class="modal-overlay" id="taskDetailModal" style="display:flex">
    <div class="modal modal-lg glass-card">
        <div class="modal-header">
            <h3><?= htmlspecialchars($selectedTask['title']) ?></h3>
            <a href="index.php?page=kanban" class="modal-close">✕</a>
        </div>
        <div class="task-detail-grid">
            <div class="task-detail-main">
                <p class="detail-desc"><?= nl2br(htmlspecialchars($selectedTask['description'])) ?></p>
                <div class="detail-meta-grid">
                    <div class="detail-meta-item">
                        <span class="meta-label">Status</span>
                        <span class="meta-value"><?= $selectedTask['status'] ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="meta-label">Priority</span>
                        <span class="priority-badge <?= priorityClass($selectedTask['priority']) ?>"><?= $selectedTask['priority'] ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="meta-label">Project</span>
                        <span class="meta-value"><?= htmlspecialchars($proj['name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="meta-label">Assigned To</span>
                        <span class="meta-value"><?= $assignee['avatar']??'👤' ?> <?= htmlspecialchars($assignee['name']??'Unassigned') ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="meta-label">Due Date</span>
                        <span class="meta-value <?= $over?'text-red':'' ?>"><?= $selectedTask['due_date'] ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="meta-label">Created</span>
                        <span class="meta-value"><?= $selectedTask['created_at'] ?></span>
                    </div>
                </div>
                <?php
                // Clients cannot move tasks — all other roles can
                $canMove = $currentUser['role'] !== 'Client';
                if ($canMove): ?>
                <div class="status-actions">
                    <p class="meta-label mb-2">Move to:</p>
                    <div class="status-btns">
                        <?php foreach (['Backlog','To Do','In Progress','Review','Completed'] as $s):
                            if ($s === $selectedTask['status']) continue; ?>
                        <form method="POST" action="index.php?page=kanban" style="display:inline">
                            <input type="hidden" name="action"  value="update_task_status">
                            <input type="hidden" name="task_id" value="<?= $selectedTask['id'] ?>">
                            <input type="hidden" name="status"  value="<?= $s ?>">
                            <button type="submit" class="btn-status"><?= $s ?></button>
                        </form>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="status-actions">
                    <p class="muted-text small">⚠ Clients cannot change task status.</p>
                </div>
                <?php endif; ?>
            </div>
            <div class="task-detail-side">
                <h4 class="section-title cyan">Comments (<?= count($selectedTask['comments']) ?>)</h4>
                <div class="comments-list">
                    <?php foreach ($selectedTask['comments'] as $c): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <strong><?= htmlspecialchars($c['userName']) ?></strong>
                            <span class="muted-text small"><?= $c['created_at'] ?></span>
                        </div>
                        <p class="comment-text"><?= htmlspecialchars($c['text']) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($selectedTask['comments'])): ?>
                    <p class="muted-text">No comments yet.</p>
                    <?php endif; ?>
                </div>
                <form method="POST" action="index.php?page=kanban&task=<?= $selectedTask['id'] ?>" class="comment-form">
                    <input type="hidden" name="action"  value="add_comment">
                    <input type="hidden" name="task_id" value="<?= $selectedTask['id'] ?>">
                    <textarea name="comment_text" placeholder="Add a comment..." rows="3" required></textarea>
                    <button type="submit" class="btn-cyan w-full mt-2">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'partials/layout_end.php'; ?>
