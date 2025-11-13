<?php
namespace Src\Repositories;

use PDO;
use Src\Config\Database;
use Throwable;

class UserRepository {
    private PDO $db;

    public function __construct(array $cfg) 
    {
        // panggil class Database dengan new, lalu connect()
        $this->db = (new Database($cfg))->connect();
    }

    public function paginate($page, $per) {
        $off = ($page - 1) * $per;
        $total = (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT id, name, email, role, created_at, updated_at
            FROM users
            ORDER BY id DESC
            LIMIT :per OFFSET :off
        ");
        $stmt->bindValue(':per', $per, PDO::PARAM_INT);
        $stmt->bindValue(':off', $off, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'total' => $total,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'page' => $page,
            'per_page' => $per,
            'last_page' => ceil($total / $per)
        ];
    }

    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT id, name, email, role, created_at, updated_at
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($name, $email, $hash, $role) {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, role)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $hash, $role]);
            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $this->find($id);
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $name, $email, $role) {
        $stmt = $this->db->prepare("
            UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?
        ");
        $stmt->execute([$name, $email, $role, $id]);
        return $this->find($id);
    }

    // 🔍 Fungsi baru: Search dengan pagination
    public function search($keyword, $page = 1, $per = 10) {
        $off = ($page - 1) * $per;
        $kw = '%' . $keyword . '%';

        // Hitung total hasil pencarian
        $stmtTotal = $this->db->prepare("
            SELECT COUNT(*) FROM users
            WHERE name LIKE :kw OR email LIKE :kw
        ");
        $stmtTotal->execute([':kw' => $kw]);
        $total = (int)$stmtTotal->fetchColumn();

        // Ambil data hasil pencarian
        $stmt = $this->db->prepare("
            SELECT id, name, email, role, created_at, updated_at
            FROM users
            WHERE name LIKE :kw OR email LIKE :kw
            ORDER BY id DESC
            LIMIT :per OFFSET :off
        ");
        $stmt->bindValue(':kw', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':per', $per, PDO::PARAM_INT);
        $stmt->bindValue(':off', $off, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'keyword' => $keyword,
            'total' => $total,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'page' => $page,
            'per_page' => $per,
            'last_page' => ceil($total / $per)
        ];
    }

    // ✏️ Fungsi baru: Patch (update sebagian kolom)
    public function patch($id, array $fields) {
        if (empty($fields)) {
            return $this->find($id);
        }

        $setParts = [];
        $params = [];

        foreach ($fields as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $params[':id'] = $id;
        $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->find($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
