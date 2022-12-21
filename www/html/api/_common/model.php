<?php

/**
 * Connexion a notre base de données.
 */
class DatabaseConnector
{
    private static $db;

    public function __construct() {
        $this::$db = new PDO(
            "mysql:host=localhost;dbname=tel;charset=utf8",
            "root",
            ""
        );
    }

    /**
     * Execute la requête SQL et renvoie le résultat.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        if (empty($params)) {
            return $this::$db->query($sql);
        }

        $result = $this::$db->prepare($sql);
        $result->execute($params);

        return $result;
    }
}

