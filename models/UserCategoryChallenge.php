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

        $st = $db->prepare("UPDATE user_category_challenges SET set_status = ? WHERE user_id = ? AND id = ?");
        foreach ($statusMap as $id => $status) {
            $st->execute([$status, $userId, $id]);
        }
        $db->commit();
    }

    public static function fetchAllWithRelations(): array
    {
        $db  = Database::getInstance();
        $sql = "
          SELECT
              u.name AS user_name,
              uc.created_at AS registered_at,
              c.name AS category_name,
              ch.type,
              ch.goal_value,
              ch.deadline,
              uc.notification_time,
              uc.set_status
          FROM  user_category_challenges uc
          JOIN  users      u ON u.id = uc.user_id
          JOIN  categories c ON c.id = uc.category_id
          LEFT JOIN challenges ch ON ch.id = uc.challenge_id
          WHERE uc.del_flg = 0
          ORDER BY uc.created_at DESC
        ";
        return $db->query($sql)->fetchAll();
    }
}
