<?php
namespace App\Core;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = Config::getInstance();
        $dsn = "mysql:host={$config->get('db_host')};dbname={$config->get('db_name')};charset=utf8mb4";
        try {
            $this->connection = new \PDO($dsn, $config->get('db_user'), $config->get('db_pass'), [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
?>