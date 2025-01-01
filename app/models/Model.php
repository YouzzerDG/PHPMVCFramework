<?php

namespace Model;

use App\Database;

#[\AllowDynamicProperties]
abstract class Model
{
    private \PDO $db;
    protected static array $table = [
        'name' => '',
        'columns' => []
    ];
    protected static array $constraints = [];
    private Model $callerClass;
    private readonly ?Model $initCaller;
    private string $tableName;

    public function __construct(Model $caller)
    {
        $this->callerClass = $caller;

        var_dump($caller);

//        if (empty($this->initCaller)) $this->initCaller = $caller;

//        var_dump([$this->initCaller, $this->callerClass]);

        $this->tableName = static::$table['name'];

        $this->db = \App\Database::getInstance();

//        echo get_called_class();

        //To prevent infinite looping
        $setRelation = true;
        if (!empty(static::$constraints)) {
            foreach (static::$constraints as $propertyName => $constraint) {
                if ($caller->{$propertyName} === null)
                    $setRelation = false;

                if (empty($caller->{$propertyName}))
                    continue;

                if ('\\' . $caller::class == $constraint['model']) {
                    $setRelation = false;
                    break;
                }
            }
        }

        if ($setRelation) $this->setRelations();
//        unset($this->callerClass);
    }

    private function createObjectFromModel(array $properties, string $model): Model
    {
        $modelInstance = new \ReflectionClass($model);

        return $modelInstance->newInstanceArgs($properties);
    }

    private function getColNames(mixed $model, bool $withTablePrefix = false): array
    {
        if (!$withTablePrefix) {
            return $model::$table['columns'];
        } else {
            $cols = [];
            foreach ($model::$table['columns'] as $col) {
                $cols[] = "{$model::$table['name']}.{$col}";
            }
            return $cols;
        }
    }

    public function getById(int $id, string $model): mixed
    {
        $tableName = $model::$table['name'];

        $statement = $this->db->prepare("SELECT * FROM `{$tableName}` WHERE `id` = :id");
        $statement->execute([':id' => $id]);

        $result = $statement->fetchObject();

        $obj = $this->createObjectFromModel(get_object_vars($result), $model);

        return $result !== false ? $obj : false;
    }

    public function get(string $model, string $query, ?array $prepare = null): mixed
    {
        $statement = $this->db->prepare($query);
        if (!is_null($prepare)) {
            foreach ($prepare as $placeholder => $parameter) {
                $statement->bindParam($placeholder, $this->callerClass->{$parameter});
            }
        }
        $statement->execute();

        $results = [];
        while ($row = $statement->fetchObject()) {
            $results[] = $this->createObjectFromModel(get_object_vars($row), $model);
        }

        return !empty($results) ? $results : false;
    }

    private function setColNames($constraint): array
    {
//        $modelSegments = explode('\\', $constraint['model']);
//        $modelColName = strtolower($modelSegments[count($modelSegments) - 1]);

        $connectedModelCols = [];
        foreach ($constraint['model']::$table['columns'] as $col) {
            if ($col !== $constraint['condition']['join']['foreign_key'])
//                          "{$constraint['model']::$table['name']}.{$col} AS {$modelColName}_{$col}";
                $connectedModelCols[] = "{$constraint['model']::$table['name']}.{$col}";
        }

        return $connectedModelCols;
    }

    private function setRelations(): void
    {
        foreach (static::$constraints as $propertyName => $constraint) {
            $connectedProperty = $this->callerClass->{$propertyName};
            $table = $constraint['join']['table'] ?? $this->callerClass::$table['name'];

            $closureArg = ['table' => $table, 'constraint' => $constraint];

            $data = match ($constraint['relationType']) {
                'oneToOne' => $this->getById($connectedProperty, $constraint['model']),
                'oneToMany' => (function () use ($closureArg) {
                    $selectQuery = implode(' ,', $this->setColNames($closureArg['constraint']));

                    if (isset($closureArg['constraint']['condition']['join']['link_table'])) {
                        $joinQuery = "INNER JOIN {$closureArg['constraint']['condition']['join']['link_table'][0]} ON {$closureArg['constraint']['condition']['join']['link_table'][0]}.{$closureArg['constraint']['condition']['join']['foreign_key']} = {$closureArg['table']}.{$closureArg['constraint']['condition']['join']['primary_key']}\n\r";
                        foreach ($closureArg['constraint']['condition']['join']['link_table'][1] as $table => $relatedKeys) {
                            $joinQuery .= "INNER JOIN $table ON {$closureArg['constraint']['condition']['join']['link_table'][0]}.$relatedKeys[0] = $table.$relatedKeys[1]\n\r";
                        }
                    }

                    $whereQuery = "WHERE {$closureArg['constraint']['condition']['where']['statement']}";

                    return $this->get($closureArg['constraint']['model'], "SELECT {$selectQuery} FROM {$closureArg['table']} {$joinQuery} {$whereQuery}", $closureArg['constraint']['prepare']);
                })(),
                'manyToOne' => (function () use ($closureArg) {
                    $selectQuery = implode(' ,', $this->setColNames($closureArg['constraint']));

                    $joinQuery = "INNER JOIN {$closureArg['constraint']['model']::$table['name']} ON {$closureArg['constraint']['model']::$table['name']}.{$closureArg['constraint']['condition']['join']['foreign_key']} = {$closureArg['table']}.{$closureArg['constraint']['condition']['join']['primary_key']}";

                    $whereQuery = "WHERE {$closureArg['constraint']['condition']['where']['statement']}";

                    return $this->get($closureArg['constraint']['model'], "SELECT {$selectQuery} FROM {$closureArg['table']} {$joinQuery} {$whereQuery}", $closureArg['constraint']['prepare']);
                })(),
                'manyToMany' => (function () use ($closureArg) {
                    $selectQuery = implode(',', $this->getColNames($closureArg['constraint']['model'], true));

                    $joinQuery = "INNER JOIN {$closureArg['constraint']['conjunction']['table']} ON {$closureArg['constraint']['conjunction']['join']}\n\r";

                    foreach ($closureArg['constraint']['conjunction'][0] as $table => $join) {
                        $joinQuery .= "INNER JOIN $table ON $join";
                    }

                    $whereQuery = "WHERE genres.id = 2";//"WHERE {$closureArg['constraint']['condition']['where']['statement']}";

                    return $this->get($closureArg['constraint']['model'], "SELECT {$selectQuery} FROM {$closureArg['table']} {$joinQuery} {$whereQuery}");
                })()
            };

//            print_r($data);

            $this->callerClass->{$constraint['property'] ?? $propertyName} = $data;

//            unset($this->callerClass->{$propertyName});
        }
    }
}