<div class="auth-container">
    <h1><?= trans('register', 'Register') ?></h1>

    <form action="/register" method="POST">
        <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">

        <div class="form-group">
            <label for="name"><?= trans('name', 'Name') ?></label>
            <input type="text"
                id="name"
                name="name"
                value="<?= h($_SESSION['old']['name'] ?? '') ?>"
                required>
        </div>

        <div class="form-group">
            <label for="email"><?= trans('email', 'Email') ?></label>
            <input type="email"
                id="email"
                name="email"
                value="<?= h($_SESSION['old']['email'] ?? '') ?>"
                required>
        </div>

        <div class="form-group">
            <label for="password"><?= trans('password', 'Password') ?></label>
            <input type="password" id="password" name="password" required>
            <small><?= trans('password_rule', 'At least 8 characters') ?></small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                <?= trans('password_confirm', 'Password (confirm)') ?>
            </label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit"><?= trans('register', 'Register') ?></button>
    </form>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= h($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error'], $_SESSION['old']); ?>
    <?php endif; ?>

    <p>
        <?= trans('already_registered', 'Already have an account?') ?>
        <a href="/login"><?= trans('login_here', 'Login here') ?></a>
    </p>
</div>