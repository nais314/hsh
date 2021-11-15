<?php

namespace bony;

class FormValidators {
    /**
    VALIDATOR FUNCTIONS------------------------------------------------------ */
    // FOR STRINGS
    public function v_alphanumeric($value, $msg){
        if(preg_match('/[^a-zA-Z0-9 ]+/',$value)){
            return $msg ? $msg: "alphanumeric characters + space only";
        }else return true;
    }

    public function v_minlength($value, $param, $msg){
        if(strlen($value) < $param){
            return  $msg ? $msg : "minimum size should be: ".$param." characters";
        }else return true;
    }

    public function v_maxlength($value, $param, $msg){
        if(strlen($value) > $param){
            return  $msg ? $msg : "maximum size should be: ".$param." characters";
        }else return true;
    }

    public function v_now(&$value, $param, $msg){
        $value = date("Y-m-d");
        return true;
    }

    // FOR NUMBERS


    public function v_int(&$value, $param, $msg){
        if(!settype($value, "int" )){
            return  $msg ? $msg : "conversion to int failed";
        }else return true;
    }

    public function v_id(&$value, $param, $msg){
        //if($value == "" || $value == 0 || $value == '0') {
        //\app::$logger->debug(__METHOD__.": ".var_export($value,true));
        if(empty($value) || $value==0){
            $value = null; 
            return true;
        }
        elseif(!settype($value, "integer" )){
            return  $msg ? $msg : "conversion to int failed";
        }else return true;
    }

    public function v_null(&$value, $param, $msg){
        if(empty($value)){
            $value = null;
        }
        return true;
    }

}//end class