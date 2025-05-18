<?php

class HomeController extends Controller
{
    /**
     * トップページ
     * ルーティング側で ['auth'] ミドルウェアが掛かっているため、
     * ここでは改めてログイン判定しない。
     */
    public function index(): void
    {
        // 翻訳読み込み
        $translations = $this->loadTranslations();

        $user = (new User())->find($_SESSION['user_id']);
        $records = [];
        if ($user && (int)$user['role'] === 0) {
            $records = UserCategoryChallenge::fetchAllWithRelations();
        }

        $this->view('home/index', [
            'title'        => $translations['home_title'] ?? 'Home',
            'translations' => $translations,
            'csrfToken'    => $this->csrfToken(),
            'records'      => $records,
        ]);
    }
}
