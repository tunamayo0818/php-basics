<div class="container">
    <h1><?= trans('welcome', 'Welcome') ?></h1>
    <p><?= trans('temporary_home_title', 'Temporary home page.') ?></p>
    <?php if (isset($_SESSION['user_name'])): ?>
        <?php
        $msg = $translations['welcome_user'] ?? ':name';
        echo '<p>' . str_replace(':name', h($_SESSION['user_name']), $msg) . '</p>';
        ?>
    <?php endif; ?>

    <?php if (!empty($records) || !empty($filters)): ?>
        <hr>
        <h2>編成一覧（検索）</h2>

        <form method="get" class="mb-3">
            <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                <input type="text" name="user" placeholder="ユーザー" value="<?= h($filters['user'] ?? '') ?>">
                <input type="text" name="category" placeholder="カテゴリ" value="<?= h($filters['category'] ?? '') ?>">
                <!-- タイプ -->
                <label><input type="radio" name="type" value="" <?= !isset($filters['type']) ? 'checked' : '' ?>> 全て</label>
                <label><input type="radio" name="type" value="0" <?= ($filters['type'] ?? '') === '0' ? 'checked' : '' ?>> 継続</label>
                <label><input type="radio" name="type" value="1" <?= ($filters['type'] ?? '') === '1' ? 'checked' : '' ?>> 目標</label>
                <input type="number" name="goal" placeholder="目標値" value="<?= h($filters['goal'] ?? '') ?>">

                <!-- 期限（from-to 日付） -->
                <div class="filter-item">
                    <label for="deadline_from" class="filter-label">期限</label>
                    <input type="date" id="deadline_from" name="deadline_from"
                        value="<?= h($filters['deadline_from'] ?? '') ?>" aria-label="期限 開始日">
                    <span class="tilde">〜</span>
                    <label for="deadline_to" class="visually-hidden">期限 終了日</label>
                    <input type="date" id="deadline_to" name="deadline_to"
                        value="<?= h($filters['deadline_to']   ?? '') ?>" aria-label="期限 終了日">
                </div>

                <!-- 通知時間 -->
                <div class="filter-item">
                    <label for="notify_from" class="filter-label">通知</label>
                    <input type="time" id="notify_from" name="notify_from"
                        value="<?= h($filters['notify_from']  ?? '') ?>" aria-label="通知時間 開始">
                    <span class="tilde">〜</span>
                    <label for="notify_to" class="visually-hidden">通知時間 終了</label>
                    <input type="time" id="notify_to" name="notify_to"
                        value="<?= h($filters['notify_to']    ?? '') ?>" aria-label="通知時間 終了">
                </div>
                <!-- SET 状態 -->
                <label>
                    <input type="radio" name="status" value="" <?= !isset($filters['status']) ? 'checked' : '' ?>>
                    全て
                </label>
                <label>
                    <input type="radio" name="status" value="0" <?= ($filters['status'] ?? '') === '0' ? 'checked' : '' ?>>
                    未設定
                </label>
                <label>
                    <input type="radio" name="status" value="1" <?= ($filters['status'] ?? '') === '1' ? 'checked' : '' ?>>
                    設定済み
                </label>
                <!-- 登録日（from-to 日付） -->
                <div class="filter-item">
                    <label for="reg_from" class="filter-label">登録</label>
                    <input type="date" id="reg_from" name="reg_from"
                        value="<?= h($filters['reg_from'] ?? '') ?>" aria-label="登録日 開始">
                    <span class="tilde">〜</span>
                    <label for="reg_to" class="visually-hidden">登録日 終了</label>
                    <input type="date" id="reg_to" name="reg_to"
                        value="<?= h($filters['reg_to'] ?? '') ?>" aria-label="登録日 終了">
                </div>

                <button type="submit" class="btn-primary">検索</button>
                <a href="/?reset=1" class="btn-primary-outline">クリア</a>
            </div>
        </form>
        <h2>編成一覧</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <?php //MOD【自己修正箇所】"<?="を"<?php echo"に変更 start 
                    ?>
                    <?php
                    /*
                    <th> <?= trans('user', 'ユーザー') ?></th>
                    <th><?= trans('category', 'カテゴリ') ?></th>
                    <th><?= trans('type', 'タイプ') ?></th>
                    <th><?= trans('goal', '目標') ?></th>
                    <th><?= trans('deadline', '期限') ?></th>
                    <th><?= trans('notification_time', '通知時間') ?></th>
                    <th><?= trans('set_status', 'SET 状態') ?></th>
                    <th><?= trans('registered_at', '登録日時') ?></th> -->
                    */
                    ?>
                    <th><?php echo trans('user', 'ユーザー') ?></th>
                    <th><?php echo trans('category', 'カテゴリ') ?></th>
                    <th><?php echo trans('type', 'タイプ') ?></th>
                    <th><?php echo trans('goal', '目標') ?></th>
                    <th><?php echo trans('deadline', '期限') ?></th>
                    <th><?php echo trans('notification_time', '通知時間') ?></th>
                    <th><?php echo trans('set_status', 'SET 状態') ?></th>
                    <th><?php echo trans('registered_at', '登録日時') ?></th>
                    <?php //MOD【自己修正箇所】"<?="を"<?php echo"に変更 end
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php //MOD【自己修正箇所】変数名の修正 start
                ?>
                <?php // foreach ($records as $r): 
                ?>
                <?php foreach ($records as $record): ?>
                    <?php //MDD【自己修正箇所】変数名の修正 end
                    ?>
                    <tr>
                        <?php // MDD【自己修正箇所】変数名の修正 | "<?="を"<?php echo"に変更 start
                        ?>
                        <?php
                        /* 
                        <td><?=  h($r['user_name']) ?></td>
                        <td><?= h($r['category_name']) ?></td>
                        <td><?=  $r['type'] === '0' ? '継続型' : '目標型' ?></td>
                        <td><?=  h($r['goal_value'] ?? '-') ?></td>
                        <td><?=  h($r['deadline']    ?? '-') ?></td>
                        <td><?=  h($r['notification_time'] ?? '-') ?></td>
                        <td><?= $r['set_status'] == 0 ? '未設定' : '設定済み' ?></td>
                        <td><?= h($r['registered_at']) ?></td>
                        */
                        ?>
                        <td><?php echo h($record['user_name']) ?></td>
                        <td><?php echo h($record['category_name']) ?></td>
                        <td><?php echo ((int)$record['type'] === 0) ? '継続型' : '目標型' ?></td>
                        <td><?php echo h($record['goal_value'] ?? '-') ?></td>
                        <td><?php echo h($record['deadline']    ?? '-') ?></td>
                        <td><?php echo h($record['notification_time'] ?? '-') ?></td>
                        <!-- SET 状態：0→未設定、1-3→設定済み -->
                        <td><?php echo $record['set_status'] == 0 ? '未設定' : '設定済み' ?></td>
                        <td><?php echo h($record['registered_at']) ?></td>
                        <?php // MDD【自己修正箇所】変数名の修正 | "<?="を"<?php echo"に変更 end
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- ページネーションリンク -->
        <?php //MOD【自己修正箇所】paginationロジックをcontrollerに記述 start 
        ?>
        <?php
        /*
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>">前へ</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === (int)$currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>">次へ</a>
            <?php endif; ?>
        </div>
        */
        ?>
        <div class="pagination">
            <?php echo $paginationLinks ?>
        </div>
        <?php //MOD【自己修正箇所】paginationロジックをcontrollerに記述 end
        ?>
    <?php endif; ?>
</div>