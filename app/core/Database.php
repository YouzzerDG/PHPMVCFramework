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

    /**
     * Returns prepared statement as if vals were an collection of array
     * */ 
    public static function bindParamsToValsRec(\PDOStatement $statement, array $placeholders, array $valsCollection): \PDOStatement 
    {
        if(empty($valsCollection) || empty($placeholders)){
            throw new \Exception("Parameter(s) empty.");
        }

        foreach($valsCollection as $valsArray) {
            $statement = self::bindParamsToVals($statement, $placeholders, $valsArray);
        }

        return $statement;
    }

    /**
     * Returns prepared statement
     * */ 
    public static function bindParamsToVals(\PDOStatement $statement, array $placeholders, array $vals): \PDOStatement 
    {
        if(count($vals) > count($placeholders)){
            throw new \Exception("Too many values match amount of parameters.");
        }

        if(count($vals) < count($placeholders)){
            throw new \Exception("Too many parameters match amount of values.");
        }
        
        $data = array_combine($placeholders, $vals);

        foreach ($data as $key => $value) {
            $statement->bindValue($key, $value);
        }

        return $statement;
    }

    /**
     * Used for INSERT, UPDATE & DELETE queries. Ensures if Exception is thrown to rollback the executed query.
     * */ 
    public static function Transaction(callable $callback): bool
    {
        $db = self::getInstance();

        try {
            $db->beginTransaction();

            $callback($db);

            if($db->commit()){
                return true;
            }
            
            return $db->rollBack();
        } catch (\PDOException $e) {
            $db->rollBack();

            var_dump($e);
        }
    }
}