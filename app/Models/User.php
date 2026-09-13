<?php
// model para manipulacao dos dados de usuarios e consultas no banco

namespace App\Models;

use App\Core\Database;
use PDO;

//========================================================
//= Model de usuarios
//========================================================

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // busca usuario ativo por email
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id_user, name, email, password, ativo 
                FROM user 
                WHERE email = :email AND ativo = 1 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch();
        return $user ?: null;
    }

    // busca usuario por id
    public function findById(int $id): ?array
    {
        $sql = "SELECT id_user, name, email, ativo 
                FROM user 
                WHERE id_user = :id 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch();
        return $user ?: null;
    }

    // cadastra novo usuario com senha criptografada
    public function create(string $name, string $email, string $password): bool
    {
        $sql = "INSERT INTO user (name, email, password, created_at, ativo) 
                VALUES (:name, :email, :password, NOW(), 1)";

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // lista todos os usuarios ativos para os filtros da dashboard
    public function getAllActive(): array
    {
        $sql = "SELECT id_user, name, email 
                FROM user 
                WHERE ativo = 1 
                ORDER BY name ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
