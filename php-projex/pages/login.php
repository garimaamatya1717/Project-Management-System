<?php
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<div class="auth-screen">
    <div class="auth-container">
        <div class="auth-logo">
            <div class="logo-icon">🚀</div>
            <h1 class="neon-text">PROJEX</h1>
        </div>
        <p class="auth-subtitle">Project Management System</p>

        <div class="glass-card auth-card neon-border-hover">
            <h2 class="auth-title">Access Terminal</h2>

            <?php if ($error): ?>
            <div class="error-box"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login" class="auth-form">
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label><span class="label-icon">✉</span> Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label><span class="label-icon">🔒</span> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-primary w-full">Initialize Login</button>
            </form>

            <div class="auth-switch">
                <a href="index.php?page=register" class="link-purple">Create New Account</a>
            </div>

        </div>
    </div>
</div>
