<?php
$currentUser  = $_SESSION['user'];
$notifications = getUserNotifications($currentUser['id']);
$unread       = unreadCount($currentUser['id']);
$activePage   = $_GET['page'] ?? 'dashboard';
?>
<div class="app-layout">
    <aside class="sidebar glass-card neon-border">
        <div class="sidebar-logo">
            <div class="logo-badge">🚀</div>
            <span class="neon-text logo-text">PROJEX</span>
        </div>
        <div class="user-info">
            <div class="user-avatar"><?= $currentUser['avatar'] ?? '👤' ?></div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($currentUser['name']) ?></div>
                <div class="user-role"><?= htmlspecialchars($currentUser['role']) ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php?page=dashboard" class="nav-item <?= $activePage==='dashboard'?'active':'' ?>"><span class="nav-icon">⊞</span> Dashboard</a>
            <a href="index.php?page=kanban"    class="nav-item <?= $activePage==='kanban'   ?'active':'' ?>"><span class="nav-icon">⧉</span> Kanban Board</a>
            <a href="index.php?page=projects"  class="nav-item <?= $activePage==='projects' ?'active':'' ?>"><span class="nav-icon">📁</span> Projects</a>
        </nav>
        <a href="index.php?page=logout" class="nav-item logout-btn"><span class="nav-icon">↩</span> Logout</a>
    </aside>

    <div class="main-content">
        <div class="topbar glass-card neon-border">
            <div class="topbar-left">
                <div class="search-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="globalSearch" placeholder="Search tasks, projects..." class="search-input">
                </div>
            </div>
            <div class="topbar-right">
                <?php if (in_array($currentUser['role'], ['Admin','Project Manager'])): ?>
                <button class="btn-cyan"   onclick="openModal('createTaskModal')">+ Task</button>
                <button class="btn-purple" onclick="openModal('createProjectModal')">+ Project</button>
                <?php endif; ?>
                <div class="notif-wrap">
                    <button class="icon-btn" onclick="toggleNotifs()" id="notifBtn">
                        🔔
                        <?php if ($unread > 0): ?><span class="notif-badge"><?= $unread ?></span><?php endif; ?>
                    </button>
                    <div class="notif-dropdown glass-card" id="notifDropdown" style="display:none">
                        <h3 class="notif-title">Notifications</h3>
                        <?php if (empty($notifications)): ?>
                        <p class="no-notif">No notifications</p>
                        <?php else: foreach ($notifications as $n): ?>
                        <form method="POST" action="index.php?page=<?= $activePage ?>">
                            <input type="hidden" name="action"   value="mark_notification_read">
                            <input type="hidden" name="notif_id" value="<?= $n['id'] ?>">
                            <button type="submit" class="notif-item <?= $n['is_read'] ? 'read' : 'unread' ?>">
                                <p class="notif-msg"><?= htmlspecialchars($n['message']) ?></p>
                                <p class="notif-time"><?= $n['created_at'] ?></p>
                            </button>
                        </form>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content">
