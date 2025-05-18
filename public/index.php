<?php

/**
 * public/index.php
 * プレーン PHP 版のフロントコントローラ
 */

// ──────────────── 1. 初期設定 ──────────────── //
// まず config を読み込み（ここでセッション設定＆ session_start() 済み）
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/core/helpers.php';


// オートローダを使うならここで登録
// spl_autoload_register(...);

// ──────────────── 2. 必要クラスを手動ロード ──────────────── //
// ※ オートローダがある場合は不要
spl_autoload_register(function ($class) {
    $dirs = ['core', 'controllers', 'models'];
    foreach ($dirs as $dir) {
        $file = dirname(__DIR__) . "/{$dir}/{$class}.php";
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
// require_once dirname(__DIR__) . '/core/Router.php';
// require_once dirname(__DIR__) . '/core/Controller.php';
// require_once dirname(__DIR__) . '/core/Model.php';
// require_once dirname(__DIR__) . '/core/Database.php';
// require_once dirname(__DIR__) . '/core/AuthMiddleware.php';
// require_once dirname(__DIR__) . '/models/User.php';

// 名前空間を使っている場合：
// use Core\Router;
// use Core\AuthMiddleware;

// ──────────────── 3. 言語設定 ──────────────── //
$lang = $_GET['lang']     ?? $_SESSION['lang'] ?? 'ja';
$_SESSION['lang'] = $lang;
$translations = include dirname(__DIR__) . "/lang/{$lang}.php";

// ──────────────── 4. ルーティング定義 ──────────────── //
$router = new Router();

// 認証関連
$router->get('/login',    'AuthController@showLoginForm');
$router->post('/login',   'AuthController@login');
$router->get('/register', 'AuthController@showRegisterForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout',   'AuthController@logout');

// 認証が必要
$router->get('/', 'HomeController@index', ['auth']);
$router->get('/challenge', 'SetController@index', ['auth']);

// ──────────────── 5. ルーティング実行 ──────────────── //
$router->dispatch();
