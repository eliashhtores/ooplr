<?php

class User
{

    private $db, $data, $sessionName, $isLoggegIn, $cookieName;

    public function __construct($user = null)
    {
        $this->db = DB::getInstance();
        $this->sessionName = Config::get('session/session_name');
        $this->cookieName = Config::get('remember/cookie_name');

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

    public function login($username = null, $password = null, $remember = false)
    {
        $user = $this->find($username);

        if ($user) {
            if($this->data()->password === Hash::make($password, $this->data()->salt)) {
                Session::put($this->sessionName, $this->data()->id);
                if ($remember) {
                    $hash = Hash::unique();
                    $hashCheck = $this->db->get('user_session', array('user_id', '=', $this->data()->id));

                    if (!$hashCheck->count()) {
                        $this->db->insert('user_session', array(
                            'user_id' => $this->data()->id,
                            'hash' => $hash
                        ));
                    } else {
                        $hash = $hashCheck->getFirst()->$hash;
                    }

                    Cookie::put($this->cookieName, $hash, Config::get('remember/cookie_expiry'));
                }
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

    public function update($fields = array(), $id = null, $table) {
        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }
        if(!$this->db->update($table, $id, $fields)) {
            throw new Exception('There was a problem updating.');
        }
    }

    public function hasPermission($key) {
        $group = $this->db->get('groups', array('id', '=', $this->data()->user_group));

        if ($group->count()) {
            $permissions = json_decode($group->getFirst()->permissions, true);

            if($permissions[$key]) {
                return true;
            }
        }
        return false;
    }

}