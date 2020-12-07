<?php

class Authorization
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function isAuthed()
    {
        return isset($_SESSION['status']) ? $_SESSION['status'] : false;
    }

    public function auth($type, $password, $username = null, $id = null)
    {
        $user = null;

        if ($type === 'institute')
            $user = $this->db->getUser($id, null);
        elseif ($type === 'special')
            $user = $this->db->getUser(null, $username);
        else
            return false;

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['status'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return json_encode(array(
                    'status' => true,
                    'id' => $user['id'],
                    'username' => $user['username']
                ));
            }
        }
        session_unset();
        session_destroy();
        return generate_error_callback('Invalid creditionals');
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        return json_encode(array(
            'status' => true
        ));
    }

    public function signup($surname, $name, $patronymic, $username, $password, $type)
    {
        if ($type === 'institute' || $type === 'full') {
            preg_match('/^[A-Za-zА-Яа-я\-]{2,35}$/', $surname, $sm);
            preg_match('/^[A-Za-zА-Яа-я\-]{2,35}$/', $name, $nm);
            preg_match('/^[A-Za-zА-Яа-я\-]{2,35}$/', $patronymic, $pm);
            if (count($sm) < 1 || count($nm) < 1 || count($pm) < 1)
                return generate_error_callback('Invalid snp credentials');
        }
        if ($type === 'special' || $type === 'full') {
            preg_match('/^[A-Za-z0-9]{3,20}$/', $username, $um);
            if (empty($um))
                return generate_error_callback('Invalid username');
            if ($this->db->isUserExists(null, $username))
                return generate_error_callback('User already exists');
        }
        if (strlen($password) > 30) return generate_error_callback('Too long password');

        $password = password_hash($password, PASSWORD_BCRYPT);

        if ($type === 'institute')
            $this->db->addUserInstitute($surname, $name, $patronymic, $password);
        elseif ($type === 'special')
            $this->db->addUserSpecial($username, $password);
        elseif ($type === 'full')
            $this->db->addUserFull($surname, $name, $patronymic, $username, $password);
        else return generate_error_callback('Invalid auth type');

        return generate_true_callback(array());
    }
}