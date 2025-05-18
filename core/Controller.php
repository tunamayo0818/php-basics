<?php

class Controller
{
    /* ---------- ビュー ---------- */

    protected function view(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . "/../views/{$view}.php";
        if (!file_exists($viewFile)) {
            throw new Exception("View {$view} not found");
        }

        extract($data, EXTR_SKIP);
        // 共通レイアウトを読み込む
        require __DIR__ . '/../views/layouts/main.php';
    }

    /* ---------- リダイレクト ---------- */

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /* ---------- バリデーション ---------- */

    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArr = explode('|', $ruleString);

            foreach ($rulesArr as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field] = "{$field} は必須です。";
                }

                if (
                    $rule === 'email' && !empty($data[$field]) &&
                    !filter_var($data[$field], FILTER_VALIDATE_EMAIL)
                ) {
                    $errors[$field] = "有効なメールアドレスを入力してください。";
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    if (mb_strlen($data[$field] ?? '') < $min) {
                        $errors[$field] = "{$field} は {$min} 文字以上で入力してください。";
                    }
                }
            }
        }

        return $errors;
    }

    /* ---------- CSRF ---------- */

    /**
     * 32 byte の乱数トークンを生成し、$_SESSION に保存して返す
     */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * 言語ファイルを読み込み連想配列で返す
     * - 参照キー: $_GET['lang'] -> $_SESSION['lang'] -> 'ja'
     * - ファイル: /lang/{lang}.php
     */
    protected function loadTranslations(): array
    {
        $lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'ja');
        $_SESSION['lang'] = $lang;
        $file = dirname(__DIR__) . "/lang/{$lang}.php";
        $trs  = file_exists($file) ? include $file : [];
        return is_array($trs) ? $trs : [];
    }
}
