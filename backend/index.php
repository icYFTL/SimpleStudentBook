<?php

error_reporting(E_ERROR | E_PARSE);

require_once 'source/database.php';
require_once 'source/auth.php';
require_once 'source/utils.php';
require_once 'source/user.php';
require_once 'source/rating.php';

session_start();
header ("Access-Control-Allow-Origin: https://icyftl.ru");
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

$db_methods = new DataBase();
$user_methods = new User();
$rating_methods = new Rating();

if (isset($_GET['method'])) {
    switch ($_GET['method']) {
        case 'getInstitutesList':
            echo generate_true_callback($db_methods->getInstitutesList());
            break;
        case 'getGroupsByInstitute':
            if (isset($_GET['institute_id'])) {
                echo generate_true_callback($db_methods->getGroupByInstitute($_GET['institute_id']));
                break;
            }
            die(generate_error_callback('Invalid institute_id'));
        case 'getStudentsByGroup':
            if (isset($_GET['group_id'])) {
                echo generate_true_callback($db_methods->getStudentsByGroup($_GET['group_id']));
                break;
            }
            die(generate_error_callback('Invalid group_id'));
        case 'getUser':
            if (isset($_SESSION['status']) && $_SESSION['status']){
                echo $user_methods->getUser();
                break;
            }
            die(generate_error_callback('Not authorized'));
        case 'getGroupRating':
            if (isset($_SESSION['status']) && $_SESSION['status']){
                echo $rating_methods->getGroupRating();
                break;
            }
            die(generate_error_callback('Not authorized'));
        case 'getGlobalRating':
            if (isset($_SESSION['status']) && $_SESSION['status']){
                echo $rating_methods->getGlobalRating();
                break;
            }
            die(generate_error_callback('Not authorized'));
        case 'getGroupSubjects':
            if (isset($_SESSION['status']) && $_SESSION['status']){
                echo $user_methods->getSubjects();
                break;
            }
            die(generate_error_callback('Not authorized'));
        case 'getStudentMarks':
            if (isset($_SESSION['status']) && $_SESSION['status']){
                echo $user_methods->getMarks();
                break;
            }
            die(generate_error_callback('Not authorized'));
        default:
            die(generate_error_callback('Invalid method'));
    }
} else{
    $data = file_get_contents('php://input');
    if ($data) {
        $data = json_decode($data, JSON_UNESCAPED_UNICODE);
        if (isset($data['method'])){
            $auth = new Authorization();
            switch ($data['method']) {
                case 'auth':
                    if ($auth->isAuthed()){
                        echo json_encode(array(
                            'status' => $_SESSION['status'],
                            'id' => $_SESSION['id'],
                            'username' => $_SESSION['username']
                        ));
                        return;
                    }
                    if (isset($data['auth_type'])) {
                        if ($data['auth_type'] === 'institute') {
                            if (isset($data['password']) && isset($data['id']))
                                echo $auth->auth('institute', $data['password'], null, $data['id']);
                            else
                                die(generate_error_callback('Empty password and(or) id passed'));
                        }
                        elseif ($data['auth_type'] === 'special'){
                            if (isset($data['password']) && isset($data['username']))
                                echo $auth->auth('special', $data['password'], $data['username'], null);
                            else
                                die(generate_error_callback('Empty password and(or) id passed'));
                        }
                    }
                    else
                        die(generate_error_callback('Specify auth type'));
                    break;
                case 'logout':
                    echo $auth->logout();
                    break;
                case 'signup':
                    if ($auth->isAuthed()){
                        echo json_encode(array(
                            'status' => $_SESSION['status'],
                            'id' => $_SESSION['id'],
                            'username' => $_SESSION['username']
                        ));
                        return;
                    }
                    if (isset($data['signup_type'])) {
                        if ($data['signup_type'] === 'institute') {
                            if (isset($data['surname']) && isset($data['name']) && isset($data['patronymic']) && isset($data['password']))
                                echo $auth->signup($data['surname'], $data['name'], $data['patronymic'], null, $data['password'], 'institute');
                            else
                                die(generate_error_callback('Invalid fields passed'));
                        }
                        elseif ($data['signup_type'] === 'special'){
                            if (isset($data['password']) && isset($data['username']))
                                echo $auth->signup(null, null, null, $data['username'], $data['password'], 'special');
                            else
                                die(generate_error_callback('Invalid fields passed'));
                        }
                        elseif($data['signup_type'] === 'full'){
                            if (isset($data['username']) && isset($data['surname']) && isset($data['name']) && isset($data['patronymic']) && isset($data['password']))
                                echo $auth->signup($data['surname'], $data['name'], $data['patronymic'], $data['username'], $data['password'], 'full');
                            else
                                die(generate_error_callback('Invalid fields passed'));
                        }
                    }
                    else
                        die(generate_error_callback('Specify auth type'));
                    break;
                case 'updateSNP':
                    if (!isset($data['name']) || !isset($data['surname']) || !isset($data['patronymic']))
                        die(generate_error_callback('Invalid SNP credentials'));

                    if (empty($data['name']) || empty($data['surname']) || empty($data['patronymic']))
                        die(generate_error_callback('Invalid SNP credentials'));
                    echo $user_methods->updateSNP($data['surname'], $data['name'], $data['patronymic']);
                    break;
                case 'updatePassword':
                    if (!isset($data['password']) || empty($data['password']))
                        die(generate_error_callback('Invalid password passed'));

                    echo $auth->updatePassword($data['password']);
                    break;
            }
        }
        else
            die(generate_error_callback('Invalid request'));
    } else
        die(generate_error_callback('Invalid request'));
}
