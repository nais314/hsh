<?php

class led{

    /** common:
     * 
     */
    public static $version = array(0,0,1);
    public static $tableName = "device_led";


    /**
     * check if installed
     * create sql tables:
     * driver_led
     */
    public static function install(){
        $filename = dirname( __FILE__ ) . DS.'device_led.sql';
        if (!self::is_installed()){

            $query = file_get_contents($filename);
            if($query === false) return false;

            /* if(!$handle = fopen($filename, "r")) return false;
            $query = fread($handle, filesize($filename));
            fclose($handle); */

            $stmt = \app::$pdo->prepare($query);
            
            if($stmt->execute() ){
                return true;
            }
            //echo \app::$pdo->errorInfo();
        }else{
            return false;
        }

    }


    public function uninstall(){

    }


    public function upgrade(){

    }

    /**
     * check if sql tables exists ;)
     */
    public static function is_installed(){
        /**
            SELECT * FROM information_schema.tables
            WHERE table_schema = 'yourdb' 
                AND table_name = 'testtable'
            LIMIT 1;
         */
/*         $val = mysql_query('select 1 from `table_name` LIMIT 1');
        if($val !== FALSE)
        {
           //DO SOMETHING! IT EXISTS!
        }
        else
        {
            //I can't find it...
        } */

        $query = <<<EOT
            SELECT * FROM information_schema.tables
            WHERE table_schema = ? 
                AND table_name = ?
            LIMIT 1;
EOT;

        $stmt = \app::$pdo->prepare($query);
        
        if($stmt->execute([ \app::$dbname, self::$tableName]) && $stmt->rowCount() > 0 ){
            return true;
        }

        return false;
    }//e is installed



}

