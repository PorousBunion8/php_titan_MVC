<?php
// model para controle das ordens de servico, calculo de comissoes e consultas com filtros

namespace App\Models;

use App\Core\Database;
use PDO;

//========================================================
//= Model de ordens de servico
//========================================================

class ServiceOrder
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // lista servicos aplicando filtros opcionais de busca
    public function getFilteredServices(array $filters = []): array
    {
        $sql = "SELECT s.id_service, s.description, s.price, s.created_at, 
                       s.finished_at, s.commission_user, u.name as user_name, u.email as user_email
                FROM service s
                INNER JOIN user u ON u.id_user = s.user_id_user
                WHERE 1=1";

        $params = [];

        // filtro por nome ou descricao do servico
        if (!empty($filters['description'])) {
            $sql .= " AND s.description LIKE :description";
            $params[':description'] = '%' . $filters['description'] . '%';
        }

        // filtro por nome do usuario responsavel
        if (!empty($filters['user_name'])) {
            $sql .= " AND u.name LIKE :user_name";
            $params[':user_name'] = '%' . $filters['user_name'] . '%';
        }

        // filtro por status do servico (pendente ou finalizado)
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'Pendente') {
                $sql .= " AND s.finished_at IS NULL";
            } elseif ($filters['status'] === 'Finalizado') {
                $sql .= " AND s.finished_at IS NOT NULL";
            }
        }

        // filtro por periodo inicial
        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(s.created_at) >= :date_start";
            $params[':date_start'] = $filters['date_start'];
        }

        // filtro por periodo final
        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(s.created_at) <= :date_end";
            $params[':date_end'] = $filters['date_end'];
        }

        $sql .= " ORDER BY s.id_service DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // calcula o valor total acumulado dos servicos prestados pelo usuario
    public function getTotalByUser(int $userId): float
    {
        $sql = "SELECT SUM(price) as total 
                FROM service 
                WHERE user_id_user = :userId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        return (float) ($result['total'] ?? 0.0);
    }

    // lista os ultimos servicos pendentes do usuario logado
    public function getPendingByUser(int $userId, int $limit = 5): array
    {
        $sql = "SELECT id_service, description, price, created_at 
                FROM service 
                WHERE user_id_user = :userId AND finished_at IS NULL 
                ORDER BY id_service DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // lista os ultimos servicos gerais para o resumo da dashboard
    public function getLatestServices(int $limit = 5): array
    {
        $sql = "SELECT id_service, description, price, created_at 
                FROM service 
                ORDER BY id_service DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // busca um servico pelo seu id com dados do usuario
    public function findById(int $id): ?array
    {
        $sql = "SELECT s.*, u.name as user_name, u.email as user_email 
                FROM service s 
                INNER JOIN user u ON u.id_user = s.user_id_user 
                WHERE s.id_service = :id 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $service = $stmt->fetch();
        return $service ?: null;
    }

    // cria novo servico com status inicial pendente
    public function create(string $description, float $price, int $userId): bool
    {
        try {
            $sql = "INSERT INTO service (description, price, user_id_user, created_at) 
                    VALUES (:description, :price, :userId, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    // atualiza os dados de um servico
    public function update(int $id, string $description, float $price): bool
    {
        try {
            $service = $this->findById($id);
            if (!$service) {
                return false;
            }

            // se ja estiver finalizado, recalcula a comissao proporcionalmente ao novo valor
            $commission = null;
            if (!empty($service['finished_at'])) {
                $commission = $this->calculateCommission($price);
            }

            $sql = "UPDATE service 
                    SET description = :description, price = :price, commission_user = :commission, update_at = NOW() 
                    WHERE id_service = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price);
            $stmt->bindValue(':commission', $commission);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    // exclui um registro de servico
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM service WHERE id_service = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // finaliza o servico, grava data de conclusao e calcula a comissao
    public function finish(int $id): bool
    {
        $service = $this->findById($id);
        if (!$service || !empty($service['finished_at'])) {
            return false;
        }

        $commission = $this->calculateCommission((float) $service['price']);

        $sql = "UPDATE service 
                SET finished_at = NOW(), commission_user = :commission, update_at = NOW() 
                WHERE id_service = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':commission', $commission);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // regra de negocio para calculo de comissao sobre o valor
    public function calculateCommission(float $price): float
    {
        if ($price <= 1000.00) {
            return round($price * 0.05, 2);
        } elseif ($price <= 10000.00) {
            return round($price * 0.10, 2);
        } else {
            return round($price * 0.20, 2);
        }
    }
}
