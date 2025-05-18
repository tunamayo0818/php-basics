<?php

class Model
{
    /** @var PDO */
    protected $db;

    /** @var string テーブル名（継承クラスで上書き） */
    protected $table;

    /** @var string 主キー */
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** 単一レコード取得 */
    public function find($id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null; // 既定 FETCH_MODE が ASSOC
    }

    /** レコード作成 */
    public function create(array $data): int
    {
        $fields = array_keys($data);
        $placeholders = rtrim(str_repeat('?,', count($fields)), ',');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            $placeholders
        );

        $stmt = $this->db->prepare($sql);
        // 値はプリペアドでバインドされるので SQLインジェクション対策を実施
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }
}
