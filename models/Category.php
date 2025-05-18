<?php
class Category extends Model
{
    protected $table = 'categories';

    /** @var array fillable 相当 */
    protected $fillable = ['name', 'type', 'del_flg'];

    /** Set 画面用：ユーザのカテゴリ＋チャレンジ配列を取得 */
    public function fetchWithChallenge(int $userId): array
    {
        $sql = "
            SELECT uc.id,
                   uc.notification_time,
                   uc.set_status,
                   c.name AS category_name,
                   ch.id  AS challenge_id,
                   ch.type,
                   ch.goal_value,
                   ch.deadline
            FROM   user_category_challenges uc
            JOIN   categories c   ON c.id = uc.category_id
            LEFT   JOIN challenges ch ON ch.id = uc.challenge_id
            WHERE  uc.user_id = ? AND uc.del_flg = 0
            ORDER  BY uc.set_status, uc.id
        ";
        $st = $this->db->prepare($sql);
        $st->execute([$userId]);
        $rows = $st->fetchAll();

        return array_map(function ($r) {
            return [
                'id'               => $r['id'],
                'notification_time' => $r['notification_time'],
                'set_status'       => $r['set_status'],
                'category'         => ['name' => $r['category_name']],
                'challenge'        => $r['challenge_id']
                    ? [
                        'id'         => $r['challenge_id'],
                        'type'       => $r['type'],
                        'goal_value' => $r['goal_value'],
                        'deadline'   => $r['deadline'],
                    ] : null,
            ];
        }, $rows);
    }
}
