<?php
/**
 * User: Diljit Ramachandran
 * Date: 6/1/15
 * Time: 1:57 PM
 * Last Modified : 19th Jan,2015
 */
session_start();
include 'Database.php';

class Authenticate
{

    public static function isLoggedIn()
    {
        return isset($_SESSION['username']);
    }

    public static function login($useremail,$password)
    {

        $db = DatabaseManager::getConnection();
        $queryString = 'SELECT * FROM UserDetails WHERE EmailId = :useremail AND Password = :password';
        $bindings = array(
            'useremail' => $useremail,
            'password'  =>  $password
        );
        $result = $db->select($queryString,$bindings);
        if ($result != false)
        {

            $_SESSION['username'] = $result[0]['Name'];
            $_SESSION['emailid'] = $result[0]['EmailId'];
            $_SESSION['department'] = $result[0]['Department'];
            $_SESSION['userid'] = $result[0]['UserId'];
            self::setUserType($result[0]['Type']);
            return isset($_SESSION['username']);
        }

        return false;
    }




    public static function redirect()
    {
        //redirect to the admin if the userType is admin else to student if the user type is user
        //redirect to student.php if the user is a student else welcome.php for teachers
        if (self::getUserType() === "STUDENT") {
            header('Location: ../student/');
            exit(0);
        }
        else if (self::getUserType() === "ADMIN")
        {
            header('Location: ../admin/');
            exit(0);
        }
        else
            header('Location: ../login/');
    }

    public static function getUserType()
        {
            return $_SESSION['type'];

        }

    public static function setUserType($userType)
    {
        if($userType == 'S')
            $_SESSION['type'] = "STUDENT";
        else
            $_SESSION['type'] = "ADMIN";

    }


    public static function logout()
    {
        session_start();
        session_destroy();
        $_SESSION = array();
        header('Location: ../login/');
        exit(0);
    }

    public static function areFieldsFilled($fields)
    {
        $flag = true;
        foreach($fields as $fieldItem)
        {
            if(!isset($fieldItem))
                $flag = false;

        }
        return $flag;
    }




}