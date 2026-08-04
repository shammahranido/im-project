<?php
class Database {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        $dsn = "pgsql:host=$host;dbname=$dbname";
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function query($sql, $params = []) {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function getPDO() {
        return $this->pdo;
    }
}
?>