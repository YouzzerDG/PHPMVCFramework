<?php namespace App;

class Database
{
    private string $host = DB_HOST;
    private string $username = DB_USER;
    private string $password = DB_PASS;
    private string $database = DB_NAME;

    private static ?\PDO $instance = null;
    protected \PDO $connection;

    private function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        try {
            $this->connection = new \PDO("mysql:dbname=$this->database;host=$this->host", $this->username, $this->password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('DB Connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            self::$instance = (new Database())->getConnection();
        }

        return self::$instance;
    }

    public static function Transaction(callable $callback): bool
    {
        $db = self::getInstance();

        try {
            $db->beginTransaction();

            var_dump($callback($db));
            exit;

            return $db->commit();
        } catch (\PDOException $e) {
            $db->rollBack();

            var_dump($e->getMessage());
        }
    }
}