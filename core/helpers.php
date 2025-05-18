<?php

/**
 * 安全な HTML エスケープ
 */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * 翻訳取得 + HTML エスケープ
 *
 * 例: trans('home') / trans('welcome', 'Welcome!')
 */
function trans(string $key, $default = null, ?array $dict = null): string
{
    //明示的に配列が渡されたら優先
    //それ以外はグローバル $translations を参照
    if ($dict === null && isset($GLOBALS['translations']) && is_array($GLOBALS['translations'])) {
        $dict = $GLOBALS['translations'];
    }

    $text = $dict[$key] ?? ($default ?? $key);
    return h((string) $text);
}
