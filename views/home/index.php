<div class="container">
    <h1><?= trans('welcome', 'Welcome') ?></h1>
    <p><?= trans('temporary_home_title', 'Temporary home page.') ?></p>
    <?php if (isset($_SESSION['user_name'])): ?>
        <?php
        $msg = $translations['welcome_user'] ?? ':name';
        echo '<p>' . str_replace(':name', h($_SESSION['user_name']), $msg) . '</p>';
        ?>
    <?php endif; ?>

    <?php if (!empty($records)): ?>
        <h2>編成一覧</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th><?= trans('user', 'ユーザー') ?></th>
                    <th><?= trans('category', 'カテゴリ') ?></th>
                    <th><?= trans('type', 'タイプ') ?></th>
                    <th><?= trans('goal', '目標') ?></th>
                    <th><?= trans('deadline', '期限') ?></th>
                    <th><?= trans('notification_time', '通知時間') ?></th>
                    <th><?= trans('set_status', 'SET 状態') ?></th>
                    <th><?= trans('registered_at', '登録日時') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                    <tr>
                        <td><?= h($r['user_name']) ?></td>
                        <td><?= h($r['category_name']) ?></td>
                        <td><?= $r['type'] === '0' ? '継続型' : '目標型' ?></td>
                        <td><?= h($r['goal_value'] ?? '-') ?></td>
                        <td><?= h($r['deadline']    ?? '-') ?></td>
                        <td><?= h($r['notification_time'] ?? '-') ?></td>

                        <td><?= $r['set_status'] == 0 ? '未設定' : '設定済み' ?></td>

                        <td><?= h($r['registered_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>