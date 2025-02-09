<?php namespace Model;

use App\Database;
use ReflectionProperty;

abstract class Model
{
    // private \PDO $db;
    protected static array $table = [
        'name' => '',
        'columns' => [],
        'foreignKeys' => [],
    ];
    protected static array $constraints = [];

    public static function all(): array|null
    {
        $model = self::getCalledModel();

        $query = "SELECT " . self::getCols($model);
        
        $query .= " FROM {$model::$table['name']}\r\n";

        $db = Database::getInstance();

        $statement = $db->prepare($query);

        $statement->execute();

        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if ($results === false || empty($results)){
            return null;
        }
            
        $dataSets = self::lazyLoad($model, $results);
        
        return self::instantiate($model, $dataSets);
    }

    public static function find(array $param): Model|null
    {
        $model = self::getCalledModel();

        $query = "SELECT " . self::getCols($model);
        
        $query .= " FROM {$model::$table['name']}\r\n";

        $key = array_key_first($param);
        $placeholder = ":" . $key;

        $query .= "WHERE {$model::$table['name']}.{$key} = $placeholder";

        $db = Database::getInstance();
        
        $statement = $db->prepare($query);

        self::bindParams($statement, $param);

        $statement->execute();

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false || empty($result)){
            return null;
        }
        
        $dataSet = self::lazyLoad($model, [$result]);

        $obj = self::instantiate($model, $dataSet);

        return $obj[0];
    }

    public static function add(mixed $data): bool
    {
        $model = self::getCalledModel();

        //TODO: make getcols also return not ID from model
        $query = "INSERT INTO {$model::$table['name']} (" . self::getCols($model, false) . ")";

        var_dump($query);

        return false;
    }

    public static function __callStatic($name, $arguments)
    {
        var_dump('call statuc');
        var_dump($name, $arguments);
    }

    //Internal model functions

    private static function getCalledModel()
    {
        return get_called_class();
    }

    private static function instantiate(string $model, array $dataSets): array 
    {
        $objs = [];
        foreach($dataSets as $dataSet) {

            $obj = new $model(...$dataSet[$model]);

            if (!empty($model::$constraints)){
                foreach($model::$constraints as $constrainedProperty => $constraint) {
                    if(isset($dataSet[$constrainedProperty]) && !empty($dataSet[$constrainedProperty])) {
                        if (is_array($obj->{$constrainedProperty})) {
                            array_walk($dataSet[$constrainedProperty], function ($dependancy) use ($obj, $constrainedProperty, $constraint) { 
                                array_push( $obj->{$constrainedProperty}, new $constraint['model'](...$dependancy[$constraint['model']]));
                            });
                        }
                        else {
                            $obj->{$property} = new $constraint['model'](...$dataSet[$constrainedProperty][$constraint['model']]);
                        }
                    }
                }
            }

            $objs[] = $obj;
        }

        return $objs;
    }

    private static function lazyLoad(string $model, array $results): array
    {
        $db = Database::getInstance();

        $data = [];

        foreach($results as &$record) {
            if(!empty($model::$constraints)) {
                foreach($model::$constraints as $property => $constraint) {
                    $query = "SELECT " . self::getCols($constraint['model']) . " FROM {$constraint['model']::$table['name']}\r\n";

                    // $query .= match($constraint['relationType']) {
                    //     'oneToOne', 'hasOne' => 'INNER',
                    //     'manyToOne', 'hasMany' => "INNER JOIN {$model::$table['name']} ON {$model::$table['name']}.{$constraint['on']['primaryKey']} = {$constraint['model']::$table['name']}.{$constraint['on']['foreignKey']} WHERE {$model::$table['name']}.{$constraint['on']['primaryKey']} = " . $record[implode('_', [$model::$table['name'], $constraint['on']['primaryKey']])]
                    // };

                    $query .= "WHERE {$constraint['model']::$table['name']}.{$constraint['on']['foreignKey']} = " . $record[implode('_', [$model::$table['name'], $constraint['on']['primaryKey']])];

                    if(!empty($db->query($query)->fetchAll(\PDO::FETCH_ASSOC))) {
                        $record[$property] = $db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
                    }
                }
            }

            $data[] = self::getDataSet($model, $record);
        }

        return $data;
    }

    private static function eagerLoad()
    {
        // For lazyloading maybe just a where, joins for eagerloading?
                    //INNER JOIN {$model::$table['name']} ON {$model::$table['name']}.{$constraint['on']['primaryKey']} = {$constraint['model']::$table['name']}.{$constraint['on']['foreignKey']} WHERE {$model::$table['name']}.{$constraint['on']['primaryKey']} = " . $record[implode('_', [$model::$table['name'], $constraint['on']['primaryKey']])]
    }

    private static function bindParams($statement, $params): void 
    {
        foreach ($params as $key => $value) {
            $placeholder = ":{$key}";
            $statement->bindParam($placeholder, $params[$key]);
        }
    }

    private static function getCols(string $model, bool $returnFormated = true, bool $withConnectedModel = false): string
    {
        $cols = [];
        //TODO: make getcols also return not ID from model
        array_map(function ($col) use ($model, $returnFormated, &$cols) {
            $cols[] = $returnFormated ? "{$model::$table['name']}.$col '{$model::$table['name']}_$col'" : $col;
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

    private static function getBlueprint(string $model, mixed $data): array
    {
        return [$model => array_combine(
            array_map(
                function ($key) use ($model) {
                    return str_replace($model::$table['name'] . "_", '', $key);
                }, 
                array_keys($data)
            ), 
        $data)];
    }

    private static function getDataSet(string $model, mixed $result): array
    {
        $dataSet = self::getBlueprint($model, $result);
        
        if (!empty($model::$constraints)) {
            foreach ($model::$constraints as $constrainedProperty => $constraint) {
                if (isset($dataSet[$model][$constrainedProperty]) && !empty($dataSet[$model][$constrainedProperty])) {
                    $property = new ReflectionProperty($model, $constrainedProperty);

                    $dependancies = [];
                    match($property->getType()->getName()) {
                        'array' => 
                            $dependancies = array_map(function($data) use (&$dataSet, $model, $constrainedProperty, $constraint) {
                                $dependancy = self::getBlueprint($constraint['model'], $data);
                                unset($dataSet[$model][$constrainedProperty]);
                                return $dependancy;
                            }, $result[$constrainedProperty]),
                        default => 
                            $dataSet[$model][$constrainedProperty] = self::getBlueprint($constraint['model'], $result[$constrainedProperty])
                    };

                    if(!empty($dependancies)){
                        $dataSet[$constrainedProperty] = $dependancies;
                    }
                }
            }
        }

        return $dataSet;
    }
}