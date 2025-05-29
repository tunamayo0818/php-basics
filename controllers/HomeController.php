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
            //MOD【自己修正箇所】変数名の修正 start
            // foreach ($keys as $k) {
            foreach ($keys as $key) {
                // if (isset($_GET[$k]) && $_GET[$k] !== '') {
                if (isset($_GET[$key]) && $_GET[$key] !== '') {
                    // $filters[$k] = trim($_GET[$k]);
                    $filters[$key] = trim($_GET[$key]);
                    //MOD【自己修正箇所】変数名の修正 end
                }
            }
            $records = UserCategoryChallenge::fetchAll($filters);
        }
        //ADD【自己修正箇所】paginationロジックをcontrollerに記述 start

        // ページネーションのロジック
        $recordsPerPage = 10;
        $totalRecords = count($records);
        $currentPage = $_GET['page'] ?? 1;
        $totalPages = ceil($totalRecords / $recordsPerPage);
        $offset = ($currentPage - 1) * $recordsPerPage;
        $paginatedRecords = array_slice($records, $offset, $recordsPerPage);

        $paginationLinks = [];
        if ($currentPage > 1) {
            $paginationLinks[] = "<a href='?page=" . ($currentPage - 1) . "'>前へ</a>";
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = $i === (int)$currentPage ? ' class="active"' : '';
            $paginationLinks[] = "<a href='?page=" . $i . "'" . $activeClass . ">" . $i . "</a>";
        }

        if ($currentPage < $totalPages) {
            $paginationLinks[] = "<a href='?page=" . ($currentPage + 1) . "'>次へ</a>";
        }
        //ADD【自己修正箇所】paginationロジックをcontrollerに記述 end

        $this->view('home/index', [
            'title'        => $translations['home_title'] ?? 'Home',
            'translations' => $translations,
            'csrfToken'    => $this->csrfToken(),
            //MOD【自己修正箇所】paginationロジックをcontrollerに記述 start
            // 'records'      => $records,
            'records'      => $paginatedRecords,
            //MOD【自己修正箇所】paginationロジックをcontrollerに記述 end
            'filters'      => $filters,
            //MOD【自己修正箇所】paginationロジックをcontrollerに記述 start
            // 'currentPage' => $currentPage,
            // 'totalPages' => $totalPages,
            'paginationLinks' => implode(' ', $paginationLinks),
            //MOD【自己修正箇所】paginationロジックをcontrollerに記述 end
        ]);
    }
}
