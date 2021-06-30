<?php
class ged_users{
    public $db;
    //connect with the database
    function __construct() {
        require_once('mysql.php'); 
        $db = new mysqlConnector();
        $this->db = $db->dataBase;
    }
    //check the user access in the system
    function haveAccess($type) {
        if($type=='right_admin'){
            $login=$_SESSION['user']['login'];
            $pass=$_SESSION['user']['password'];
            $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ? AND password = ? AND right_admin = 1");
            $r->execute([$login,$pass]);
            $n = $r->fetchAll();
            if(count($n)==1){
            }else{
                $_SESSION['message']=array("success","Vous n'avez pas l'acces");
                header('location:index.php');
            }
        }
        if($type=='right_up'){
            $login=$_SESSION['user']['login'];
            $pass=$_SESSION['user']['password'];
            $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ? AND password = ? AND right_up = 1");
            $r->execute([$login,$pass]);
            $n = $r->fetchAll();
            if(count($n)==1){
            }else{
                $_SESSION['message']=array("success","Vous n'avez pas l'acces");
                header('location:index.php');
            }
        }
        if($type=='right_read'){
            $login=$_SESSION['user']['login'];
            $pass=$_SESSION['user']['password'];
            $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ? AND password = ? AND right_read = 1");
            $r->execute([$login,$pass]);
            $n = $r->fetchAll();
            if(count($n)==1){
            }else{
                $_SESSION['message']=array("success","Vous n'avez pas l'acces");
                header('location:index.php');
            }
        }
        
    }
    //User login 
    function logMe($login,$pass) {
        $pass=  sha1($pass);
        $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ? AND password = ?");
        $r->execute([$login,$pass]);
        $n = $r->fetchAll();
        if(count($n)==1){
            $_SESSION['user']=array(
                "login"=>$n['0']->login,
                "password"=>$n['0']->password,
                "username"=>$n['0']->username,
                "right_read"=>$n['0']->right_read,
                "right_up"=>$n['0']->right_up,
                "right_admin"=>$n['0']->right_admin
            );
            $_SESSION['message']=array("success","Vous avez été connecté");
            header('location:index.php');
        }else{
            $_SESSION=array();
            $_SESSION['message']=array("error","Vous n'avez pas l'acces");
            header('location:index.php');
        }
        
    }
    //Confirm that the user is logged
    function islogged() {
        if(isset($_SESSION['user'])){
            $login=$_SESSION['user']['login'];
            $pass=$_SESSION['user']['password'];
            $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ? AND password = ?");
            $r->execute([$login,$pass]);
            $n = $r->fetchAll();
            if(count($n)==1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    //getting the user token from db
    function getToken($login){
        $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ?");
        $r->execute([$login]);
        $n = $r->fetchAll();
        return $n['0']->token;
    }
    //Confirming the user token
    function isToken($token){
        $r = $this->db->prepare("SELECT * FROM ged_users WHERE token = ?");
        $r->execute([$token]);
        $n = $r->fetchAll();
        if(count($n)==1){
            return true;
        }else{
            return false;
        }
    }
    //chaging user's password
    function changePass($pass,$token){
        $r = $this->db->prepare("UPDATE ged_users SET password=?,token=0 WHERE token=?");
        $r->execute([
            sha1($pass),
            $token
        ]);
    }
    //Checking the existence of the user in the db
    function userNotExist($login){
        $r = $this->db->prepare("SELECT * FROM ged_users WHERE login = ?");
        $r->execute([$login]);
        $n = $r->fetchAll();
        if(count($n)==1){
            return false;
        }else{
            return true;
        }
    }
    //Creation of users
    function createUser($login,$password,$username,$rightRead,$rightUp,$rightAdmin,$token) {
        $r = $this->db->prepare("INSERT INTO ged_users SET login=?, password=?, username=?, right_read=?, right_up=?, right_admin=?, token=?");
        $r->execute([
            $login,
            $password,
            $username,
            $rightRead,
            $rightUp,
            $rightAdmin,
            $token
        ]);
    }
    //Getting all users from db
    function getAllUsers() {
        $r = $this->db->prepare("SELECT * FROM ged_users");
        $r->execute([]);
        return $r->fetchAll();
    }
    //updating user
    function reinitUsers($id) {
        $pass=  sha1('@perso');
        $token=  sha1(time());
        $r = $this->db->prepare("UPDATE ged_users SET password=?,token=? WHERE id=?");
        $r->execute([$pass,$token,$id]);
    }
    //changing user's rights
    function changeRights($rightRead,$rightUp,$rightAdmin,$id) {
        
        $r = $this->db->prepare("UPDATE ged_users SET right_read=?,right_up=?, right_admin=? WHERE id=?");
        $r->execute([$rightRead,$rightUp,$rightAdmin,$id]);
    }
}
