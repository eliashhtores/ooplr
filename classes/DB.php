<?php

class DB
{

    private static $instance = null;
    private $pdo,
            $query,
            $error = false,
            $result,
            $count = 0 ;

    private function __construct()
    {
        try {
            $this->pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)){
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function query($sql, $params = array())
    {
        $this->error = false;
        if ($this->query = $this->pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $this->query->bindValue($x, $param);
                    $x++;
                }
            }

            if ($this->query->execute()) {
                $this->result = $this->query->fetchAll(PDO::FETCH_OBJ);
                $this->count = $this->query->rowCount();
            } else {
                $this->error = $this->query->errorInfo()[2];
                //echo $this->query->errorInfo()[2];
            }
        }
        return $this;
    }

    public function action($action, $table, $where = array())
    {
        if (count($where === 3)) {
            $operators = array('=', '>', '<', '<=', '>=');

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if (!$this->query($sql, array($value))->error()) {
                    return $this;
                }
            }
        }
        return $this->error;
    }

    public function get($table, $where)
    {
        return $this->action('SELECT *', $table, $where);
    }

    public function delete($table, $where)
    {
        return $this->action('DELETE', $table, $where);
    }

    public function insert($table, $fields = array())
    {
        if (count($fields)) {
            $keys = array_keys($fields);
            $values = '';
            $x = 1;

            foreach ($fields as $field) {
                $values .= '?';
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                $x++;
            }

            $sql = "INSERT INTO {$table} (" . implode(', ', $keys) . ") VALUES ({$values})";
            if (!$this->query($sql, $fields)->error()) {
                return true;
            }
        }
        return $this->error();
    }

    public function update($table, $id, $fields)
    {
        $set = '';
        $x = 1;

        foreach ($fields as $name => $value)
        {
            $set .= "{$name} = ?";
            if($x < count($fields)) {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
        if (!$this->query($sql, $fields)->error()) {
            return true;
        }
        return $this->error;
    }

    public function results()
    {
        return $this->result;
    }

    public function getFirst()
    {
        return $this->results()[0];
    }

    public function count()
    {
        return $this->count;
    }

    public function error()
    {
        return $this->error;
    }

}