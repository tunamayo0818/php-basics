<?php
class UserCategoryChallenge extends Model
{
    protected $table = 'user_category_challenges';
    protected $fillable = [
        'user_id',
        'category_id',
        'challenge_id',
        'notification_time',
        'del_flg',
        'set_status'
    ];

    /** set_status を一括更新（RegistrationController::updateSetStatus 相当） */
    public static function bulkUpdateSetStatus(int $userId, array $statusMap): void
    {
        $db = Database::getInstance();
        $db->beginTransaction();
        $db->prepare("UPDATE user_category_challenges SET set_status = 0 WHERE user_id = ?")
            ->execute([$userId]);

        $stmt = $db->prepare("UPDATE user_category_challenges SET set_status = ? WHERE user_id = ? AND id = ?");
        foreach ($statusMap as $id => $status) {
            $stmt->execute([$status, $userId, $id]);
        }
        $db->commit();
    }

    //MOD【自己修正箇所】start :クエリを一つにまとめる
    // public static function fetchAllWithRelations(): array
    private static function getBaseQuery(): string
    //MOD【自己修正箇所】start :クエリを一つにまとめる
    {
        //MOD【自己修正箇所】start :クエリを一つにまとめる
        // $db  = Database::getInstance();
        // $sql = "
        //     SELECT
        //         u.name AS user_name,
        //         uc.created_at AS registered_at,
        //         c.name AS category_name,
        //         ch.type,
        //         ch.goal_value,
        //         ch.deadline,
        //         uc.notification_time,
        //         uc.set_status
        //     FROM  user_category_challenges uc
        //     JOIN  users      u ON u.id = uc.user_id
        //     JOIN  categories c ON c.id = uc.category_id
        //     LEFT JOIN challenges ch ON ch.id = uc.challenge_id
        //     WHERE uc.del_flg = 0
        //     ORDER BY uc.created_at DESC
        // ";
        // return $db->query($sql)->fetchAll();

        return <<<SQL
        SELECT
            u.name           AS user_name,
            uc.created_at    AS registered_at,
            c.name           AS category_name,
            ch.type,
            ch.goal_value,
            ch.deadline,
            uc.notification_time,
            uc.set_status
        FROM user_category_challenges uc
        JOIN users      u  ON u.id = uc.user_id
        JOIN categories c  ON c.id = uc.category_id
        LEFT JOIN challenges ch ON ch.id = uc.challenge_id
        WHERE uc.del_flg = 0
        SQL;
    }

    //MOD【自己修正箇所】start :検索メソッドとデータ取得メソッドを分ける
    // public static function fetchAllWithFilters(array $f): array
    public static function fetchAll(array $filters = []): array
    //MOD【自己修正箇所】end :検索メソッドとデータ取得メソッドを分ける

    {
        $db   = Database::getInstance();
        //MOD【自己修正箇所】start :検索メソッドとデータ取得メソッドを分ける
        // $sql  = "
        //     SELECT u.name AS user_name, uc.created_at AS registered_at,
        //         c.name AS category_name, ch.type, ch.goal_value,
        //         ch.deadline, uc.notification_time, uc.set_status
        //     FROM   user_category_challenges uc
        //     JOIN   users      u ON u.id = uc.user_id
        //     JOIN   categories c ON c.id = uc.category_id
        //     LEFT   JOIN challenges ch ON ch.id = uc.challenge_id
        //     WHERE  uc.del_flg = 0
        // ";
        //MOD【自己修正箇所】end :検索メソッドとデータ取得メソッドを分ける
        $bind = [];

        //MOD【自己修正箇所】start :検索メソッドとデータ取得メソッドを分ける
        $sql = static::getBaseQuery();

        // 2) 動的 WHERE 句
        $sql .= static::buildFilterSql($filters, $bind);


        /* 動的 WHERE 句 */
        // if ($f['user'] ?? '') {
        //     $sql .= " AND u.name LIKE ? ";
        //     $bind[] = '%' . $f['user'] . '%';
        // }
        // if ($f['category'] ?? '') {
        //     $sql .= " AND c.name LIKE ? ";
        //     $bind[] = '%' . $f['category'] . '%';
        // }
        // if (($f['type'] ?? '') !== '') {
        //     $sql .= " AND ch.type = ? ";
        //     $bind[] = (int)$f['type'];
        // }
        // if ($f['goal'] ?? '') {
        //     $sql .= " AND ch.goal_value = ? ";
        //     $bind[] = (int)$f['goal'];
        // }
        /* 日時・日付範囲系 */
        // if ($f['deadline_from'] ?? '') {
        //     $sql .= " AND ch.deadline >= ? ";
        //     $bind[] = $f['deadline_from'];
        // }
        // if ($f['deadline_to'] ?? '') {
        //     $sql .= " AND ch.deadline <= ? ";
        //     $bind[] = $f['deadline_to'];
        // }
        // if ($f['notify_from'] ?? '') {
        //     $sql .= " AND uc.notification_time >= ? ";
        //     $bind[] = $f['notify_from'];
        // }
        // if ($f['notify_to'] ?? '') {
        //     $sql .= " AND uc.notification_time <= ? ";
        //     $bind[] = $f['notify_to'];
        // }

        //MOD【自己修正箇所】start
        // if (($f['status'] ?? '') !== '') {
        //     //MOD【自己修正箇所】start
        //     // $sql .= " AND uc.set_status = ? ";
        //     // $bind[] = (int)$f['status'];
        //     if ($f['status'] === '0') {
        //         $sql .= " AND uc.set_status = 0 ";
        //     } elseif ($f['status'] === '1') {
        //         $sql .= " AND uc.set_status IN (1,2,3) ";
        //     }
        //MOD【自己修正箇所】end
        // }
        // if ($f['reg_from'] ?? '') {
        //     $sql .= " AND uc.created_at >= ? ";
        //     $bind[] = $f['reg_from'] . ' 00:00:00';
        // }
        // if ($f['reg_to'] ?? '') {
        //     $sql .= " AND uc.created_at <= ? ";
        //     $bind[] = $f['reg_to'] . ' 23:59:59';
        // }
        //MOD【自己修正箇所】end :検索メソッドとデータ取得メソッドを分ける
        $sql .= " ORDER BY uc.created_at DESC ";

        $stmt = $db->prepare($sql);
        $stmt->execute($bind);
        return $stmt->fetchAll();
    }

    //ADD【自己修正箇所】start :検索メソッドとデータ取得メソッドを分ける
    private static function buildFilterSql(array $filter, array &$bind): string
    {
        $clauses = [];
        /* 動的 WHERE 句 */
        if (!empty($filter['user'])) {
            $clauses[] = 'u.name LIKE ?';
            $bind[]    = "%{$filter['user']}%";
        }
        if (!empty($filter['category'])) {
            $clauses[] = 'c.name LIKE ?';
            $bind[]    = "%{$filter['category']}%";
        }
        if (isset($filter['type']) && $filter['type'] !== '') {
            $clauses[] = 'ch.type = ?';
            $bind[]    = (int)$filter['type'];
        }
        if (!empty($filter['goal'])) {
            $clauses[] = 'ch.goal_value = ?';
            $bind[]    = (int)$filter['goal'];
        }
        if (!empty($filter['deadline_from'])) {
            $clauses[] = 'ch.deadline >= ?';
            $bind[]    = $filter['deadline_from'];
        }
        if (!empty($filter['deadline_to'])) {
            $clauses[] = 'ch.deadline <= ?';
            $bind[]    = $filter['deadline_to'];
        }
        if (!empty($filter['notify_from'])) {
            $clauses[] = 'uc.notification_time >= ?';
            $bind[]    = $filter['notify_from'];
        }
        if (!empty($filter['notify_to'])) {
            $clauses[] = 'uc.notification_time <= ?';
            $bind[]    = $filter['notify_to'];
        }

        // SET 状態
        if (isset($filter['status']) && $filter['status'] !== '') {
            if ($filter['status'] === '0') {
                $clauses[] = 'uc.set_status = 0';
            } else {
                $clauses[] = 'uc.set_status IN (1,2,3)';
            }
        }

        if (!empty($filter['reg_from'])) {
            $clauses[] = 'uc.created_at >= ?';
            $bind[]    = $filter['reg_from'] . ' 00:00:00';
        }
        if (!empty($filter['reg_to'])) {
            $clauses[] = 'uc.created_at <= ?';
            $bind[]    = $filter['reg_to'] . ' 23:59:59';
        }

        return $clauses
            ? ' AND ' . implode(' AND ', $clauses)
            : '';
    }
    //MOD【自己修正箇所】end :検索メソッドとデータ取得メソッドを分ける
}
