<?php
/**
 * Secure Login and Session Functions
 **/
session_start();
class SecureSession{
    var $securepath;
    var $loginpage;
    function SecureSession($securepath,$loginpage=false,$level=0){
        $this->securepath = $securepath;
        $this->loginpage = $loginpage;
    }
    function login($un,$pw,$redir=false,$other=array()){
        $users = parse_ini_file($this->securepath,true);
        //print_r($users);
        foreach($users as $k=>$u){
            //echo "$un: {$u['username']} , $pw(".md5($pw)."): {$u['md5']} \n";
            if($u['username']==$un&&$u['md5']==md5($pw)){
                $_SESSION['xs_uname']=$un;
                $_SESSION['xs_level']=$u['accesslevel'];
                $_SESSION['xs_uid']=$k;
                $_SESSION['xs_login']=time();
                foreach($other as $o){
                    $_SESSION[$o]=$u[$o];
                }
                if($redir)
                    header('Location: '.$redir);
                return true;
            }
        }
        return false;
    }
    function check($level=0,$timeout=30,$return=false){
        if($_SESSION['xs_uname']!=''&&$_SESSION['xs_login']>time()-$timeout*60&&$_SESSION['xs_level']+1>$level){
            return $_SESSION['xs_level'];
        }else{
            if(!$return)
                $return=$_SERVER['PHP_SELF'];
            if($this->loginpage)
                $this->logout($this->loginpage.'?redir='.$return);
            $this->logout();
            return false;
        }
    }
    function logout($redir=false){
        unset($_SESSION['xs_uname']);
        unset($_SESSION['xs_level']);
        unset($_SESSION['xs_uid']);
        unset($_SESSION['xs_login']);
        if($redir)
            header('Location: '.$redir);
    }
};
?>
