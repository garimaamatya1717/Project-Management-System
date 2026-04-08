<?php
include 'partials/layout_start.php';

$userProjects = array_values(getUserProjects($currentUser));
$filterStatus = $_GET['status'] ?? 'All';
$searchQuery  = $_GET['search'] ?? '';

$filtered = array_filter($userProjects, function($p) use ($filterStatus, $searchQuery) {
    $matchSearch = !$searchQuery || stripos($p['name'], $searchQuery) !== false || stripos($p['description'], $searchQuery) !== false;
    $matchFilter = $filterStatus === 'All' || $p['status'] === $filterStatus;
    return $matchSearch && $matchFilter;
});
$filtered = array_values($filtered);

$statusFilters = ['All','Active','On Hold','Completed'];
$statusIconMap = ['Active'=>'📈','On Hold'=>'⏸','Completed'=>'✅'];
?>
<div class="page-header">
    <h2 class="page-title neon-text">Projects</h2>
    <p class="muted-text">Manage and track all your projects</p>
</div>

<div class="glass-card filter-bar">
    <form method="GET" action="index.php" class="filter-form">
        <input type="hidden" name="page" value="projects">
        <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search projects..." class="search-input">
        </div>
        <div class="filter-btns">
            <?php foreach ($statusFilters as $sf): ?>
            <button type="submit" name="status" value="<?= $sf ?>"
                class="filter-btn <?= $filterStatus===$sf?'active':'' ?>"><?= $sf ?></button>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<div class="projects-grid">
    <?php foreach ($filtered as $project):
        $projectTasks   = array_values(getTasksByProject($project['id']));
        $completedTasks = count(array_filter($projectTasks, fn($t) => $t['status'] === 'Completed'));
        $totalTasks     = count($projectTasks);
        $members        = getProjectMembers($project['id']);
        $deadlineSoon   = strtotime($project['deadline']) <= strtotime('+7 days');
    ?>
    <div class="project-card glass-card">
        <div class="project-card-header">
            <div class="project-icon-wrap">
                <div class="project-icon">📁</div>
                <div>
                    <h3><?= htmlspecialchars($project['name']) ?></h3>
                    <p class="muted-text small"><?= htmlspecialchars(mb_substr($project['description'],0,80)) ?>...</p>
                </div>
            </div>
            <span class="status-badge <?= statusClass($project['status']) ?>">
                <?= $statusIconMap[$project['status']]??'' ?> <?= $project['status'] ?>
            </span>
        </div>
        <div class="progress-wrap mt-4">
            <div class="progress-labels">
                <span class="muted-text">Overall Progress</span>
                <span><?= $project['progress'] ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill animated" style="width:<?= $project['progress'] ?>%"></div>
            </div>
        </div>
        <div class="project-stats">
            <div class="pstat">
                <span class="pstat-icon cyan">👥</span>
                <span class="muted-text small">Team</span>
                <strong><?= count($members) ?></strong>
            </div>
            <div class="pstat">
                <span class="pstat-icon purple">✅</span>
                <span class="muted-text small">Tasks</span>
                <strong><?= $completedTasks ?>/<?= $totalTasks ?></strong>
            </div>
            <div class="pstat">
                <span class="pstat-icon <?= $deadlineSoon?'red':'cyan' ?>">📅</span>
                <span class="muted-text small">Due</span>
                <strong class="<?= $deadlineSoon?'text-red':'' ?>"><?= $project['deadline'] ?></strong>
            </div>
        </div>
        <div class="team-avatars">
            <div class="muted-text small mb-2">Team Members</div>
            <div class="avatar-stack">
                <?php foreach (array_slice($members,0,5) as $m): ?>
                <div class="avatar-circle" title="<?= htmlspecialchars($m['name']) ?>"><?= $m['avatar']??'👤' ?></div>
                <?php endforeach; ?>
                <?php if (count($members) > 5): ?>
                <div class="avatar-circle avatar-more">+<?= count($members)-5 ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($filtered)): ?>
<div class="glass-card empty-state">
    <div class="empty-icon">📁</div>
    <p class="muted-text">No projects found</p>
</div>
<?php endif; ?>

<?php include 'partials/layout_end.php'; ?>
