<?php


namespace Zler\Biz\Dao;

use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Type;

class Connection extends DoctrineConnection
{
    public function update($tableExpression, array $data, array $identifier, array $types = array())
    {
        $this->checkFieldNames(array_keys($data));

        return parent::update($tableExpression, $data, $identifier, $types);
    }

    public function insert($tableExpression, array $data, array $types = array())
    {
        $this->checkFieldNames(array_keys($data));

        $data = $this->addBackSlash($data);

        return $this->parentInsert($tableExpression, $data, $types);
    }

    /**
     * Inserts a table row with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string                                                               $table Table name
     * @param array<string, mixed>                                                 $data  Column-value pairs
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types Parameter types
     *
     * @return int The number of affected rows.
     *
     * @throws Exception
     */
    public function parentInsert($table, array $data, array $types = [])
    {
        if (empty($data)) {
            return $this->executeStatement('INSERT INTO `' . $table . '` () VALUES ()');
        }

        $columns = [];
        $values  = [];
        $set     = [];

        foreach ($data as $columnName => $value) {
            $columns[] = $columnName;
            $values[]  = $value;
            $set[]     = '?';
        }

        return $this->executeStatement(
            'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ')' .
            ' VALUES (' . implode(', ', $set) . ')',
            $values,
            is_string(key($types)) ? $this->extractTypeValues($columns, $types) : $types
        );
    }

    /**
     * Extract ordered type list from an ordered column list and type map.
     *
     * @param array<int, string>                                                   $columnList
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types
     *
     * @return array<int, int|string|Type|null>|array<string, int|string|Type|null>
     */
    private function extractTypeValues(array $columnList, array $types)
    {
        $typeValues = [];

        foreach ($columnList as $columnIndex => $columnName) {
            $typeValues[] = $types[$columnName] ?? ParameterType::STRING;
        }

        return $typeValues;
    }

    public function addBackSlash($data)
    {
        $backSlashData = [];
        foreach ($data as $name => $value){
            $name = '`'.$name.'`';
            $backSlashData[$name] = $value;
        }
        return $backSlashData;
    }

    public function checkFieldNames($names)
    {
        foreach ($names as $name) {
            if (!ctype_alnum(str_replace('_', '', $name))) {
                throw new \InvalidArgumentException('Field name is invalid.');
            }
        }

        return true;
    }

    public function transactional(\Closure $func, \Closure $exceptionFunc = null)
    {
        $this->beginTransaction();
        try {
            $result = $func($this);
            $this->commit();

            return $result;
        } catch (\Exception $e) {
            $this->rollBack();
            !is_null($exceptionFunc) && $exceptionFunc($this);
            throw $e;
        }
    }
}