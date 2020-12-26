<?php

use SQLite3;

class DataBase
{
    private $conn = null;

    public function __construct()
    {
        $this->conn = new SQLite3("px.db") or die('database down');
        $this->initDb();
    }

    private function initDb()
    {
        $this->conn->exec('CREATE TABLE IF NOT EXISTS institutes (id INTEGER PRIMARY KEY, name TEXT unique);');
        $this->conn->exec('CREATE TABLE IF NOT EXISTS groups     (id INTEGER PRIMARY KEY, name TEXT unique, i_id INTEGER, FOREIGN KEY (i_id) REFERENCES institutes(id));');
        $this->conn->exec('CREATE TABLE IF NOT EXISTS students   (id INTEGER PRIMARY KEY, surname TEXT NOT NULL DEFAULT "", name TEXT NOT NULL DEFAULT "", patronymic TEXT NOT NULL DEFAULT "", username TEXT DEFAULT NULL, password TEXT, group_id INTEGER, FOREIGN KEY (group_id) REFERENCES groups(id));');
        $this->conn->exec('CREATE TABLE IF NOT EXISTS teachers   (id INTEGER PRIMARY KEY, surname TEXT NOT NULL DEFAULT "", name TEXT NOT NULL DEFAULT "", patronymic TEXT NOT NULL DEFAULT "", username TEXT DEFAULT NULL, password TEXT);');
        $this->conn->exec('CREATE TABLE IF NOT EXISTS marks      (id INTEGER PRIMARY KEY, subject_id INTEGER NOT NULL, mark TEXT NOT NULL, student_id INTEGER NOT NULL, event INTEGER NOT NULL, FOREIGN KEY (student_id) REFERENCES students(id), FOREIGN KEY (subject_id) REFERENCES subjects(id));');
        $this->conn->exec('CREATE TABLE IF NOT EXISTS subjects   (id INTEGER PRIMARY KEY, name TEXT NOT NULL, group_id INTEGER NOT NULL, host_id INTEGER NOT NULL, semester INTEGER NOT NULL, pass_type TEXT NOT NULL, FOREIGN KEY (host_id) REFERENCES teachers(id), FOREIGN KEY (group_id) REFERENCES groups(id));');
    }

    public function getInstitutesList()
    {
        $result = $this->conn->query("SELECT * FROM institutes;") or die('Something went wrong');
        $tmp = array();
        while ($_ = $result->fetchArray(SQLITE3_ASSOC))
            array_push($tmp, $_);
        return array('values' => $tmp);
    }

    public function getGroupByInstitute($institute_id)
    {
        $result = $this->conn->query("SELECT * FROM groups WHERE i_id={$institute_id};") or die('Something went wrong');
        $tmp = array();
        while ($_ = $result->fetchArray(SQLITE3_ASSOC))
            array_push($tmp, $_);
        return array('values' => $tmp);
    }

    public function getStudentsByGroup($group_id)
    {
        $result = $this->conn->query("SELECT id, name, surname, patronymic, username FROM students WHERE group_id={$group_id};") or die('Something went wrong');
        $tmp = array();
        while ($_ = $result->fetchArray(SQLITE3_ASSOC))
            array_push($tmp, $_);
        return array('values' => $tmp);
    }

    public function isUserExists($id = null, $username = null)
    {
        if (!is_null($id))
            return !empty($this->conn->query("SELECT id FROM students WHERE id={$id};")->fetchArray());
        if (!is_null($username)) {
            return !empty($this->conn->query("SELECT id FROM students WHERE username='{$username}';")->fetchArray());
        }

        return null;
    }

    public function getStudent($id = null, $username = null)
    {
        $user = null;
        if ($id)
            $user = $this->conn->query("SELECT * FROM students WHERE id={$id};")->fetchArray(SQLITE3_ASSOC);
        if ($username)
            $user = $this->conn->query("SELECT * FROM students WHERE username='{$username}';")->fetchArray(SQLITE3_ASSOC);

        return $user;
    }

    public function updateSNP($surname, $name, $patronymic, $id)
    {
        $this->conn->exec("UPDATE students SET surname='{$surname}', name='{$name}', patronymic='{$patronymic}' WHERE id={$id}");
    }

    public function addUserSpecial($username, $password)
    {
        $this->conn->exec("INSERT INTO students (username, password) VALUES ('{$username}', '{$password}')");
    }

    public function addUserInstitute($surname, $name, $patronymic, $password)
    {
        $this->conn->exec("INSERT INTO students (surname, name, patronymic, password) VALUES ('{$surname}', '{$name}', '{$patronymic}','{$password}')");
    }

    public function addUserFull($surname, $name, $patronymic, $username, $password)
    {
        $this->conn->exec("INSERT INTO students (surname, name, patronymic, username, password) VALUES ('{$surname}', '{$name}', '{$patronymic}', '{$username}', '{$password}')");
    }

    public function updatePassword($student_id, $password)
    {
        $this->conn->exec("UPDATE students SET password='{$password}' WHERE id={$student_id};");
    }

    public function getTeacher($id = null, $username = null)
    {
        $user = null;
        if ($id)
            $user = $this->conn->query("SELECT * FROM teachers WHERE id={$id};")->fetchArray(SQLITE3_ASSOC);
        if ($username)
            $user = $this->conn->query("SELECT * FROM teachers WHERE username='{$username}';")->fetchArray(SQLITE3_ASSOC);

        return $user;
    }

    public function addTeacherSpecial($username, $password)
    {
        $this->conn->exec("INSERT INTO teachers (username, password) VALUES ('{$username}', '{$password}')");
    }

    public function addTeacherInstitute($surname, $name, $patronymic, $password)
    {
        $this->conn->exec("INSERT INTO teachers (surname, name, patronymic, password) VALUES ('{$surname}', '{$name}', '{$patronymic}','{$password}')");
    }

    public function addTeacherFull($surname, $name, $patronymic, $username, $password)
    {
        $this->conn->exec("INSERT INTO teachers (surname, name, patronymic, username, password) VALUES ('{$surname}', '{$name}', '{$patronymic}', '{$username}', '{$password}')");
    }

    public function addMark($student_id, $subject_id, $mark, $mdate)
    {
        $this->conn->exec("INSERT INTO marks (subject_id, mark, student_id, mdate) VALUES ({$subject_id}, {$mark}, {$student_id}, {$mdate})");
    }

    public function updateMark($markId, $newMark)
    {
        $this->conn->exec("UPDATE marks SET mark={$newMark} WHERE id={$markId}");
    }

    public function delMark($markId)
    {
        $this->conn->exec("DELETE FROM marks WHERE id={$markId};");
    }

    public function getStudentsMarks($student_id)
    {
        $result = array();
        $pre_res = $this->conn->query("SELECT * FROM marks WHERE student_id={$student_id};");
        while ($tmp = $pre_res->fetchArray(SQLITE3_ASSOC)) {
            array_push($result, $tmp);
        }

        return array('values' => $result);
    }

    public function addSubject($name, $group_id, $host_id, $pass_type)
    {
        $this->conn->exec("INSERT INTO subjects (name, group_id, host_id, pass_type) VALUES ('{$name}', {$group_id}, {$host_id}, '{$pass_type}');");
    }

    public function delSubject($subject_id)
    {
        $this->conn->exec("DELETE FROM subjects WHERE id={$subject_id};");
    }

    public function getGroupSubjects($group_id)
    {
        $result = array();
        $pre_res = $this->conn->query("SELECT * FROM subjects WHERE group_id={$group_id};");
        while ($tmp = $pre_res->fetchArray(SQLITE3_ASSOC))
            array_push($result, $tmp);

        foreach ($result as &$val)
            $val['t_snp'] = $this->getTeacherSNP($val['host_id']);
        return array('values' => $result);
    }


    public function getTeacherSNP($teacher_id)
    {
        return $this->conn->query("SELECT surname || ' ' || name || ' ' || patronymic as snp FROM teachers WHERE id={$teacher_id};")->fetchArray(SQLITE3_ASSOC)['snp'];
    }

    public function getStudentSNP($student_id)
    {
        return $this->conn->query("SELECT surname || ' ' || name || ' ' || patronymic as snp FROM students WHERE id={$student_id};")->fetchArray(SQLITE3_ASSOC)['snp'];
    }

    public function getStudentAverage($student_id)
    {
        return $this->conn->query("SELECT avg(mark) as average FROM marks WHERE student_id={$student_id};")->fetchArray(SQLITE3_ASSOC)['average'];
    }

    public function getGroupRating($group_id)
    {
        $result = [];
        $students = $this->getStudentsByGroup($group_id)['values'];

        foreach ($students as $student) {
            $value = $this->getStudentAverage($student['id']);
            if (is_null($value))
                $value = 0;

            array_push($result, array('value' => $value, 'snp' => $this->getStudentSNP($student['id']), 'id' => $student['id'], 'username' => $student['username']));
        }


        $vals = array_column($result, 'value');
        array_multisort($vals, SORT_DESC, $result);

        return array('values' => $result);
    }

    public function getGlobalRating($student_id)
    {
        $query = $this->conn->query("SELECT student_id as id, avg(mark) as average FROM marks GROUP BY student_id ORDER BY avg(mark) DESC LIMIT 15;");
        $temp_query = [];
        $result = [];
        while ($tmp = $query->fetchArray(SQLITE3_ASSOC))
            array_push($temp_query, $tmp);
        
        foreach ($temp_query as $student) {
            $user = $this->getStudent($student['id']);
            $value = $this->getStudentAverage($student['id']);
            if (is_null($value))
                $value = 0;

            array_push($result, array('snp' => $this->getStudentSNP($student['id']), 'value' => $value, 'id' => $student['id'], 'username' => $user['username']));
        }



        $vals = array_column($result, 'value');
        array_multisort($vals, SORT_DESC, $result);

        return array('values' => $result);
    }

}