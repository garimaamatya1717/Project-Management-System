        </div><!-- end page-content -->
    </div><!-- end main-content -->
</div><!-- end app-layout -->

<?php
$currentUser = $_SESSION['user'];
$allUsers    = getAllUsers();
$allProjects = getAllProjects();
if (in_array($currentUser['role'], ['Admin','Project Manager'])): ?>

<!-- Create Task Modal -->
<div class="modal-overlay" id="createTaskModal" style="display:none">
    <div class="modal glass-card">
        <div class="modal-header">
            <h3>Create New Task</h3>
            <button class="modal-close" onclick="closeModal('createTaskModal')">✕</button>
        </div>
        <form method="POST" action="index.php?page=dashboard">
            <input type="hidden" name="action"      value="create_task">
            <input type="hidden" name="return_page" value="<?= $_GET['page'] ?? 'dashboard' ?>">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="Task title" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Task description" rows="3"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['Backlog','To Do','In Progress','Review','Completed'] as $s): ?>
                        <option value="<?= $s ?>"><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority">
                        <?php foreach (['Low','Medium','High','Critical'] as $p): ?>
                        <option value="<?= $p ?>"><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Assign To</label>
                    <select name="assignedTo">
                        <?php foreach ($allUsers as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Project</label>
                    <select name="projectId">
                        <?php foreach ($allProjects as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="dueDate" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-ghost" onclick="closeModal('createTaskModal')">Cancel</button>
                <button type="submit" class="btn-cyan">Create Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal-overlay" id="createProjectModal" style="display:none">
    <div class="modal glass-card">
        <div class="modal-header">
            <h3>Create New Project</h3>
            <button class="modal-close" onclick="closeModal('createProjectModal')">✕</button>
        </div>
        <form method="POST" action="index.php?page=projects">
            <input type="hidden" name="action" value="create_project">
            <div class="form-group">
                <label>Project Name</label>
                <input type="text" name="name" placeholder="Project name" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Project description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
            </div>
            <div class="form-group">
                <label>Team Members</label>
                <div class="members-grid">
                    <?php foreach ($allUsers as $u): ?>
                    <label class="member-check">
                        <input type="checkbox" name="teamMembers[]" value="<?= $u['id'] ?>">
                        <span><?= $u['avatar'] ?? '👤' ?> <?= htmlspecialchars($u['name']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-ghost" onclick="closeModal('createProjectModal')">Cancel</button>
                <button type="submit" class="btn-purple">Create Project</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
