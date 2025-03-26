<?php

namespace App\Database;

class DatabaseHandler
{
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $table, array $criteria = []): array
    {
        $query = "SELECT * FROM $table";
        if (!empty($criteria)) {
            $conditions = [];
            foreach ($criteria as $key => $value) {
                $conditions[] = "$key = :$key";
            }
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->connection->prepare($query);
        $stmt->execute($criteria);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Add other CRUD methods (insert, update, delete) as needed
}