<?php

namespace bony;

class AccessValidators {
    /**
    VALIDATOR FUNCTIONS------------------------------------------------------ */

    /**
     TODO: parameters are not used :()
     */
    public function v_loggedin($value, $msg){ //return $msg ? $msg : "ACCESS DENIED.";
        if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
            $_SESSION['QUERY_STRING'] = '?'.$_SERVER['QUERY_STRING']; // login forwards to this url
            echo "<script>window.location.href='?r=site/login'</script>";
            die("ACCESS DENIED.");
            //return $msg ? $msg : "ACCESS DENIED.";
        }else return true;
    }
   
    public function v_local($value, $msg){
        if($_SERVER[SERVER_NAME] != 'localhost'){
            return $msg ? $msg : "ACCESS DENIED.";
        }else return true;
    }
        # alias
        public function v_localhost($value, $msg) { v_local($value, $msg); }


    public function v_deny($value, $msg){
            return $msg ? $msg : "ACCESS DENIED.";
    }
        #alias
        public function v_denyAll($value, $msg){
                return $msg ? $msg : "ACCESS DENIED.";
        }
    
    public function v_token($value, $msg){
        $modelName = \app::$module.'Model';

        if(isset($_POST[$modelName::$tableName])){
            if(!isset($_REQUEST['token']) || $_REQUEST['token'] != $_SESSION['token']) {
                return $msg ? $msg : "TOKEN ERROR.";
            }else return true;
        }else return true;
    }

}//end class