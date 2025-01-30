<?php namespace Model;

use App\Database;
use ReflectionProperty;

#[\AllowDynamicProperties]
abstract class Model
{
    // private \PDO $db;
    protected static array $table = [
        'name' => '',
        'columns' => [],
        'foreignKeys' => [],
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

        $query = "SELECT " . self::getCols($model);

        // if (!empty($model::$constraints)){
        //     // $query .= self::getCols($model, true);

        //     foreach ($model::$constraints as $constraint) {
        //         $query .= match($constraint['relationType']) {
        //             'oneToOne', 'hasOne' => 'INNER',
        //             'manyToOne' => 'LEFT'
        //         };
        //     }
        // }
        // else {
        //     $query .= self::getCols($model);
        // }
        
        $query .= " FROM {$model::$table['name']}\r\n";

        $db = Database::getInstance();

        $statement = $db->prepare($query);

        $statement->execute();

        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if ($results === false || empty($results)){
            return null;
        }
            
        foreach($results as &$record) {
            if(!empty($model::$constraints)) {
                foreach($model::$constraints as $property => $constraint) {
                    $query = "SELECT " . self::getCols($constraint['model']) . " FROM {$constraint['model']::$table['name']}\r\n";

                    $query .= match($constraint['relationType']) {
                        'oneToOne', 'hasOne' => 'INNER',
                        'manyToOne', 'hasMany' => "INNER JOIN {$model::$table['name']} ON {$model::$table['name']}.{$constraint['on']['primaryKey']} = {$constraint['model']::$table['name']}.{$constraint['on']['foreignKey']} WHERE {$model::$table['name']}.{$constraint['on']['primaryKey']} = " . $record[implode('_', [$model::$table['name'], $constraint['on']['primaryKey']])]
                    };
                    
                    $record[$property] = $db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
                }
            }

            // var_dump($record);

            var_dump(self::getSubsets($model, $record));
        }
        

        // var_dump(self::getSubsets($model, $results));
        // $dataSet = [];


        // var_dump($result);
        // exit;

        // foreach($result as $data) {
        //     $dataSets = self::getSubsets($model, $data);

        //     $obj = new $model(...$dataSets[$model]);

        //     if (!empty($model::$constraints)){
        //         foreach($model::$constraints as $property => $constraint) {
        //             $obj->{$property} = new $constraint['model'](...$dataSets[$constraint['model']]);
        //         }
        //     }

        //     $dataSet[] = $obj;
        // }

        // return $dataSet;
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

    private static function getCols(string $model, bool $withConnectedModel = false): string
    {
        $cols = [];

        array_map(function ($col) use ($model, &$cols) {
            $cols[] = "{$model::$table['name']}.$col '{$model::$table['name']}_$col'";
        }, $model::$table['columns']);


        if (!empty($model::$constraints) && $withConnectedModel) {
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
                'oneToOne', 'hasOne' => 'INNER',
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
            foreach ($model::$constraints as $constrainedProperty => $constraint) {
                $property = new ReflectionProperty($model, $constrainedProperty);

                match($property->getType()->getName()) {
                    'array' => 
                        $dataSets[$model][$constrainedProperty] = $result[$constrainedProperty],
                    default => 
                        $dataSets[$model][$constrainedProperty] = array_filter($result, function ($key) use ($constraint) {
                        $model = $constraint['model'];
                        return str_contains($key, $model::$table['name']) === true;
                    }, ARRAY_FILTER_USE_KEY)
                };

                match(gettype($dataSets[$model][$constrainedProperty])){
                    'array' => $keys = array_map(function ($key) use ($constraint) {
                            $model = $constraint['model'];
                            return str_replace($model::$table['name'] . "_", '', $key);
                        }, $dataSets[$model][$constrainedProperty]),
                };

                // $keys = array_map(function ($key) use ($constraint) {
                //     $model = $constraint['model'];
                //     return str_replace($model::$table['name'] . "_", '', $key);
                // }, array_keys($dataSets[$model][$constrainedProperty]));


                var_dump($keys);

                // $dataSets[$model][$constrainedProperty] = array_combine($keys, $dataSets[$model][$constrainedProperty]);
            }
        }

// var_dump($dataSets);

        return $dataSets;
    }
}