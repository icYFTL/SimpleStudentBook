<?php

class User
{
    private $db;
    private $id;

    public function __construct()
    {
        $this->db = new DataBase();
        $this->id = $_SESSION['id'];
    }

    public function updateSNP($surname, $name, $patronymic){
        preg_match('/^[A-Za-zА-Яа-я\-]{2,35}$/', $surname, $sm);
        preg_match('/^[A-Za-zА-Яа-я\-]{2,35}$/', $name, $nm);

        //hehe 1 l1k3 3nj3cT1on$

        if (count($sm) < 1 || count($nm) < 1)
            return generate_error_callback('Invalid snp credentials');

        $user = $this->db->getStudent($_SESSION['id']);
        if ($user['surname'] === '' && $user['name'] === '' && $user['patronymic'] === ''){
            $this->db->updateSNP($surname, $name, $patronymic, $_SESSION['id']);
            return generate_true_callback();
        }
        return generate_error_callback('You can\'t change SNP credentials.');
    }

    public function getUser(){
        return generate_true_callback($this->db->getStudent($this->id, null));
    }

    public function getSubjects(){
        $group_id = $this->db->getStudent($this->id, null)['group_id'];
        return generate_true_callback($this->db->getGroupSubjects($group_id));
    }

    public function getMarks(){
        return generate_true_callback($this->db->getStudentsMarks($this->id));
    }
}