<div class="auth-container">
    <h1><?= trans('login', 'login') ?></h1>

    <form action="/login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">

        <div class="form-group">
            <label for="email"><?= trans('email', 'Email') ?></label>
            <input type="text"
                id="email"
                name="email"
                value="<?= h($_SESSION['old']['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="password"><?= trans('password', 'Password') ?></label>
            <input type="password" id="password" name="password">
        </div>

        <button type="submit"><?= trans('login', 'Login') ?></button>
    </form>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= h($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error'], $_SESSION['old']); ?>
    <?php endif; ?>

    <p>
        <?= trans('not_registered', 'Not registered') ?>
        <a href="/register"><?= trans('register_here', 'Register here') ?></a>
    </p>
</div>