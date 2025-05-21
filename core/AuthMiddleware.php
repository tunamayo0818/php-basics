<?php

class AuthMiddleware
{
    public function handle(): bool
    {
        //ADD【自己修正箇所】start
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        //ADD【自己修正箇所】end
        // ログイン済みは通過
        if (!empty($_SESSION['user_id'])) {
            //ADD【自己修正箇所】start
            //ログイン中に/loginダイレクトアクセスで強制ログアウトするバグの修正
            if ($path === '/login') {
                header('Location: /');
                exit;
            }
            //ADD【自己修正箇所】end
            return true;
        }
        if ($path !== '/') {
            $_SESSION['error'] = 'ログインが必要です。';
        }
        header('Location: /login');
        exit;
    }
}
