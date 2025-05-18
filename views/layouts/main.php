<?php

/**
 * 全ページ共通レイアウト
 * 変数:
 *   - $title        : ページタイトル
 *   - $viewFile     : 各ビューのファイルパス（Controller::view() がセット）
 *   - $translations : 言語配列（任意）
 */
$lang = h($_SESSION['lang'] ?? 'ja');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Untitled') ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <?php if (isset($_SESSION['csrf_token'])): ?>
        <meta name="csrf-token" content="<?= h($_SESSION['csrf_token']) ?>">
    <?php endif; ?>
</head>

<body>
    <header>
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav>
                <ul>
                    <li><a href="/"><?= trans('home', 'Home') ?></a></li>
                    <li><a href="/challenge"><?= trans('challenge', 'Challenge') ?></a></li>
                    <li><a href="/logout"><?= trans('logout', 'Logout') ?></a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>

    <main>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= h($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= h($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php include $viewFile; ?>
    </main>

    <footer>
        <div class="language-switcher">
            <a href="?lang=ja">日本語</a> |
            <a href="?lang=en">English</a>
        </div>
    </footer>
</body>

</html>