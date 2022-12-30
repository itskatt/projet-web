<?php

require_once "../../../config.php";

/**
 * Connexion a notre base de données.
 */
class DatabaseConnector
{
    private static $db;

    public function __construct()
    {
        $this::$db = new PDO(
            "mysql:host=localhost;dbname=tel;charset=utf8",
            DB_USER,
            DB_PASSWORD
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

    /**
     * Expose la méthode PDO::beginTransaction()
     */
    public function beginTransaction(): bool
    {
        return $this::$db->beginTransaction();
    }

    /**
     * Expose la méthode PDO::commit()
     */
    public function commit(): bool
    {
        return $this::$db->commit();
    }

    /**
     * Expose la méthode PDO::rollBack()
     */
    public function rollBack(): bool
    {
        return $this::$db->rollBack();
    }
}
