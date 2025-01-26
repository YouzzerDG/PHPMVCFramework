<?php namespace Model;

use App\Database;

#[\AllowDynamicProperties]
abstract class Model
{
    // private \PDO $db;
    protected static array $table = [
        'name' => '',
        'columns' => [],
        'foreign_keys' => [],
    ];
    protected static array $constraints = [];

    public static function __callStatic($name, $arguments)
    {
        var_dump('call statuc');
        var_dump($name, $arguments);
    }

    private static function getCalledModel()
    {
        return get_called_class();
    }

    // public static function where(mixed $params): Model|null
    // {
    //     $model = self::getCalledModel();
        
    //     var_dump($model, $params);


    // }

    public static function all(): array|null
    {
        $model = self::getCalledModel();

        $query = "SELECT " . self::getCols($model) . " FROM {$model::$table['name']}\r\n";

        if (!empty($model::$constraints)){
            $query .= self::createJoin($model);
        }
        

        var_dump($query);
        $db = Database::getInstance();

        $statement = $db->prepare($query);

        $statement->execute();

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if ($result === false)
            return null;
        
        $dataSet = [];


        var_dump($result);
        exit;

        foreach($result as $data) {
            $dataSets = self::getSubsets($model, $data);

            $obj = new $model(...$dataSets[$model]);

            if (!empty($model::$constraints)){
                foreach($model::$constraints as $property => $constraint) {
                    $obj->{$property} = new $constraint['model'](...$dataSets[$constraint['model']]);
                }
            }

            $dataSet[] = $obj;
        }

        return $dataSet;
    }

    public static function find(mixed $param): Model|null
    {
        $model = self::getCalledModel();

        $query = "SELECT " . self::getCols($model) . " FROM {$model::$table['name']}\r\n";

        if (!empty($model::$constraints)){
            $query .= self::createJoin($model);
        }
        
        $key = key($param);
        $placeholder = ":" . $key;

        $query .= "\r\nWHERE {$model::$table['name']}.{$key} = $placeholder";

        $db = Database::getInstance();

        $statement = $db->prepare($query);

        self::bindParam($statement, $param);

        $statement->execute();

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false)
            return null;
        
        $dataSets = self::getSubsets($model, $result);

        $obj = new $model(...$dataSets[$model]);

        if (!empty($model::$constraints)){
            foreach($model::$constraints as $property => $constraint) {
                $obj->{$property} = new $constraint['model'](...$dataSets[$constraint['model']]);
            }
        }

        return $obj;
    }

    private static function bindParam($statement, $params): void 
    {
        foreach ($params as $key => $value) {
            $placeholder = ":{$key}";
            $statement->bindParam($placeholder, $params[$key]);
        }
    }

    private static function getCols(string $model): string
    {
        $cols = [];

        array_map(function ($col) use ($model, &$cols) {
            $cols[] = "{$model::$table['name']}.$col '{$model::$table['name']}_$col'";
        }, $model::$table['columns']);


        if (!empty($model::$constraints)) {
            foreach ($model::$constraints as $constraint) {
                $constrainedModel = $constraint['model'];

                array_map(function ($col) use ($constrainedModel, &$cols) {
                    $cols[] = "{$constrainedModel::$table['name']}.$col '{$constrainedModel::$table['name']}_$col'";
                }, $constrainedModel::$table['columns']);
            }
        }

        return implode(", \r\n", $cols);
    }

    private static function createJoin(string $model): string
    {
        if (empty($model::$constraints)) 
            return '';
        
        return implode("\r\n", array_map(function ($constraint) {
            $joinedTable = $constraint['model']::$table['name'];

            $joinType = match($constraint['relationType']) {
                'oneToOne' => 'INNER',
                'manyToOne' => 'LEFT'
            };

            return "$joinType JOIN $joinedTable ON {$constraint['on']}";
        }, $model::$constraints));
    }

    private static function getSubsets(string $model, mixed $result): array
    {
        $dataSets = [];

        $dataSets[$model] = array_filter($result, function ($key) use ($model) {
            return str_contains($key, $model::$table['name']) === true;
        }, ARRAY_FILTER_USE_KEY);

        $dataSets[$model] = array_combine(
            array_map(function ($key) use ($model) {
                return str_replace($model::$table['name'] . "_", '', $key);
            }, array_keys($dataSets[$model])), $dataSets[$model]
        );

        if (!empty($model::$constraints)) {
            foreach ($model::$constraints as $constraint) {

                $dataSets[$constraint['model']] = array_filter($result, function ($key) use ($constraint) {
                    $model = $constraint['model'];
                    return str_contains($key, $model::$table['name']) === true;
                }, ARRAY_FILTER_USE_KEY);

                $keys = array_map(function ($key) use ($constraint) {
                    $model = $constraint['model'];
                    return str_replace($model::$table['name'] . "_", '', $key);
                }, array_keys($dataSets[$constraint['model']]));


                $dataSets[$constraint['model']] = array_combine($keys, $dataSets[$constraint['model']]);
            }
        }

        return $dataSets;
    }
}