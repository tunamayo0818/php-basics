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
        $records      = [];
        $filters      = [];
        $user = (new User())->find($_SESSION['user_id']);
        if ($user && (int)$user['role'] === 0) {
            $keys = [
                'user',
                'category',
                'type',
                'goal',
                'deadline_from',
                'deadline_to',
                'notify_from',
                'notify_to',
                'status',
                'reg_from',
                'reg_to'
            ];
            //MOD【自己修正箇所】start
            // foreach ($keys as $k) {
            foreach ($keys as $key) {
                // if (isset($_GET[$k]) && $_GET[$k] !== '') {
                if (isset($_GET[$key]) && $_GET[$key] !== '') {
                    // $filters[$k] = trim($_GET[$k]);
                    $filters[$key] = trim($_GET[$key]);
                    //MOD【自己修正箇所】end
                }
            }
            $records = UserCategoryChallenge::fetchAll($filters);
        }

        $this->view('home/index', [
            'title'        => $translations['home_title'] ?? 'Home',
            'translations' => $translations,
            'csrfToken'    => $this->csrfToken(),
            'records'      => $records,
            'filters'      => $filters,
        ]);
    }
}
