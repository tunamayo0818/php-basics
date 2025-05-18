<?php

/**
 * models/User.php
 * 役割: 認証機能 + ダッシュボード用データ取得
 */
class User extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    /* -------------------------------------------------
       認証関連 ― AuthController から呼ばれるメソッド
    --------------------------------------------------*/

    /** email から 1 行取得（未削除のみ） */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND del_flg = 0 LIMIT 1";
        $st  = $this->db->prepare($sql);
        $st->execute([$email]);
        return $st->fetch() ?: null;
    }

    /** 新規登録 */
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data); // core/Model::create() を利用
    }

    /** パスワード検証 */
    public function verifyPassword(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }

    /**
     * 指定ユーザのカテゴリ＋チャレンジ一覧を返す
     */
    public static function categoryChallenges(int $userId): array
    {
        $db  = Database::getInstance();
        $sql = "
            SELECT uc.id,
                   uc.notification_time,
                   uc.set_status,
                   c.name AS category_name,
                   ch.id AS challenge_id,
                   ch.type,
                   ch.goal_value,
                   ch.deadline
            FROM   user_category_challenges uc
            JOIN   categories  c  ON c.id  = uc.category_id
            LEFT   JOIN challenges ch ON ch.id = uc.challenge_id
            WHERE  uc.user_id = ? AND uc.del_flg = 0
            ORDER  BY uc.set_status, uc.id
        ";
        $st = $db->prepare($sql);
        $st->execute([$userId]);
        return $st->fetchAll();
    }
}
