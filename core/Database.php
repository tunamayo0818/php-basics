<?php

/**
 * シングルトン PDO ラッパー
 * 名前空間なし（グローバル） … 他の Core クラスと合わせる
 */
class Database
{
    /** @var self|null */
    private static $instance = null;

    /** @var PDO */
    private $pdo;

    /** コンストラクタ（外部から呼ばせない） */
    private function __construct()
    {
        // 設定を読み込む（配列が返ってくる）
        $config = include __DIR__ . '/../config/database.php';

        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['database'],
                $config['charset']
            );

            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );

            // 文字セット & 照合順序を確実に適用
            $this->pdo->exec(
                "SET NAMES {$config['charset']} COLLATE {$config['collation']}"
            );
        } catch (PDOException $e) {
            die('データベース接続に失敗しました: ' . $e->getMessage());
        }
    }

    /** @return PDO */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
