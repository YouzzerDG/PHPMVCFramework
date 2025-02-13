<?php namespace Model;

use App\Database;
use App\Exceptions;
use ReflectionProperty;

abstract class Model
{
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
        if(empty($param)){
            return null;
        }
        
        $model = self::getCalledModel();

        $query = "SELECT " . self::getCols($model);
        
        $query .= " FROM {$model::$table['name']}\r\n";

        $key = array_key_first($param);
        $placeholder = ":" . $key;

        $query .= "WHERE {$model::$table['name']}.{$key} = $placeholder";

        $db = Database::getInstance();
        
        $statement = $db->prepare($query);

        $statement = Database::bindParamsToVals($statement, [$placeholder], $param);

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
        if(empty($data) || is_null($data)) {
            return false;
        }

        return Database::Transaction(function(\PDO $db) use ($data) {
            $model = self::getCalledModel();

            $cols = self::getCols($model, false, false);

            $preparedCols = array_map(function($col) {
                return ':' . trim($col);
            }, explode(',', $cols));

            $preparedColsString = implode(", \r\n", $preparedCols);

            $query = "INSERT INTO {$model::$table['name']} ($cols) VALUES ($preparedColsString)";

            $statement = $db->prepare($query);

            if(!is_array($data[array_key_first($data)])){
                $statement = Database::bindParamsToVals($statement, $preparedCols, $data);
            }
            else{
                $statement = Database::bindParamsToValsRec($statement, $preparedCols, $data);
            }

            $statement->execute();
        });
    }

    public static function update(mixed $data): bool
    {
        return false;
    }

    public static function delete(mixed $data): bool
    {
        return false; 
    }

    public static function __callStatic($name, $arguments)
    {
        var_dump('call statuc');
        var_dump($name, $arguments);
    }

    //Internal model functions

    private static function getCalledModel(): string
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

    private static function getCols(string $model, bool $returnFormated = true, bool $returnWithId = true , bool $withConnectedModel = false): string
    {
        $cols = [];

        array_map(function ($col) use ($model, $returnFormated, $returnWithId, &$cols) {
            if ($returnWithId === true || ($returnWithId === false && $col !== 'id')) {
                $cols[] = $returnFormated ? "{$model::$table['name']}.$col '{$model::$table['name']}_$col'" : $col;
            }
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
        return [$model => \App\Utils::subTrimArrayKeys($model::$table['name'].'_', $data)];
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