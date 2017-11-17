<?php

class User
{

    private $db;

    public function __construct($user = null)
    {
        $this->db = DB::getInstance();
    }

    public function create($fields = array())
    {
        if ($this->db->insert('users', $fields) === true) {
            return true;
        } else {
            throw new Exception('There was a problem creating the account.' . var_dump($this->db->error()));
        }
    }

}