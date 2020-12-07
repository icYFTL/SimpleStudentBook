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

    public function getUser(){
        return generate_true_callback($this->db->getUser($this->id, null));
    }

    public function getSubjects(){
        $group_id = $this->db->getUser($this->id, null)['group_id'];
        return generate_true_callback($this->db->getGroupSubjects($group_id));
    }

    public function getMarks(){
        return generate_true_callback($this->db->getStudentsMarks($this->id));
    }
}