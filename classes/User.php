<?php

class User
{

    private $db, $data, $sessionName, $isLoggegIn;

    public function __construct($user = null)
    {
        $this->db = DB::getInstance();
        $this->sessionName = Config::get('session/session_name');

        if (!$user) {
            if (Session::exists($this->sessionName)) {
                $user = Session::get($this->sessionName);

                if ($this->find($user)) {
                    $this->isLoggegIn = true;
                } else {
                        //process logout
                }
            }
        } else {
            $this->find($user);
        }
    }

    public function create($fields = array())
    {
        if ($this->db->insert('users', $fields) === true) {
            return true;
        } else {
            throw new Exception('There was a problem creating the account.' . var_dump($this->db->error()));
        }
    }

    public function find($user = null) {
        if ($user) {
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->db->get('users', array($field, '=', $user));

            if ($data->count()){
                $this->data = $data->getFirst();
                return true;
            }
        }
        return false;
    }

    public function login($username = null, $password= null)
    {
        $user = $this->find($username);

        if ($user) {
            if($this->data()->password === Hash::make($password, $this->data()->salt)) {
                Session::put($this->sessionName, $this->data()->id);
                return true;
            }
            return false;
        }
    }

    public function logout()
    {
        Session::delete($this->sessionName);
    }

    public function data()
    {
        return $this->data;
    }

    public function isLoggedIn()
    {
        return $this->isLoggegIn;
    }

}