<?php

namespace Core;

class AuthMiddleware
{
    public function handle(): bool
    {
        // ログイン済みは通過
        if (!empty($_SESSION['user_id'])) {
            return true;
        }
        // 直アクセスしてきたパスを取得
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        // トップページ "/" ならエラーを入れず静かにリダイレクト
        if ($path !== '/') {
            $_SESSION['error'] = 'ログインが必要です。';
        }
        header('Location: /login');
        exit;
    }
}
