<?php

/**
 * Connexion a notre base de données.
 */
class DatabaseConnector
{
    private static $db;

    /**
     * Effectue une connection a la base de données.
     */
    private function getDb(): PDO
    {
        if ($this->db === null) {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=tel;charset=utf8",
                "root",
                ""
            );
        }

        return $this->db;
    }

    /**
     * Execute la requête SQL et renvoie le résultat.
     */
    protected function query(string $sql, array $params = []): PDOStatement
    {
        if (empty($params)) {
            return $this->getDb()->query($sql);
        }

        $result = $this->getDb()->prepare($sql);
        $result->execute($params);

        return $result;
    }
}

