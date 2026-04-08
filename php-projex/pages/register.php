<?php
$error = $_SESSION['reg_error'] ?? '';
unset($_SESSION['reg_error']);
$roles = ['Admin', 'Project Manager', 'Developer', 'Designer', 'Client','QA',];
$selectedRole = $_POST['role'] ?? 'Developer';
?>
<div class="auth-screen">
    <div class="auth-container">
        <div class="auth-logo">
            <div class="logo-icon">🚀</div>
            <h1 class="neon-text">PROJEX</h1>
        </div>
        <p class="auth-subtitle">Project Management System</p>

        <div class="glass-card auth-card neon-border-hover">
            <h2 class="auth-title">Create Account</h2>

            <?php if ($error): ?>
            <div class="error-box"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=register" class="auth-form">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label><span class="label-icon">👤</span> Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label><span class="label-icon">✉</span> Email</label>
                    <input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label><span class="label-icon">🔒</span> Password</label>
                    <input type="password" name="password" placeholder="Create a password" required>
                </div>

                <div class="form-group">
                    <label><span class="label-icon">🛡</span> Role</label>
                    <div class="role-grid">
                        <?php foreach ($roles as $r): ?>
                        <label class="role-option <?= $selectedRole === $r ? 'selected' : '' ?>">
                            <input type="radio" name="role" value="<?= $r ?>" <?= $selectedRole === $r ? 'checked' : '' ?>>
                            <?= $r ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn-secondary w-full">Create Account</button>
            </form>

            <div class="auth-switch">
                <a href="index.php?page=login" class="link-cyan">Already have an account? Login</a>
            </div>
        </div>
    </div>
</div>
