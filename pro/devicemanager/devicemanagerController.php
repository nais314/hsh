<?php

// namespace devicemanager;

class devicemanagerController {

    /**
    init autoloader ------------------------------------------------------------ */
    
    static function autoloader($class){
        //error_reporting(E_ERROR);
        #overhead: os compatibility
        $class = str_replace('\\', DS, $class );
        //$class = $class.'.php';

        //echo $class;
    
        # load from /pro/lib/
        if(file_exists(\app::$modulepath.DS.'drivers'.DS.$class.DS.$class.'.php')){
          include_once(\app::$modulepath.DS.'drivers'.DS.$class.DS.$class.'.php');
        }
        elseif(file_exists(\app::$modulepath.DS.'drivers'.DS.$class.'.php')){
            include_once(\app::$modulepath.DS.'drivers'.DS.$class.'.php');
        }
        else {
        }
        //die();
    }


    public function beforeAction(){
    
        spl_autoload_register('self::autoloader');

    }

    /*-----------------------------------------------------------------------*/

    public function test(){
        $content = \app::render('device_create');
        include \app::layout();

        /* error_reporting(E_ALL);
        $led = new led();
        echo ($led->is_installed())? "is" : "isnot";
        echo "<br>";

        echo (led::install())? "ok": "err"; */
    }

    /**
     * scan drivers folder, return folder names
     * a driver is either installed (more or less (sql)) or in the disabled folder
     */
    public function installed_drivers(){
        $dir = dirname(__FILE__).DS.'drivers';
        $filelist = glob($dir.DS."*", GLOB_ONLYDIR );
        foreach ($filelist as &$value) {
            $value = basename($value);
        }

        sort($filelist, SORT_STRING);

        return $filelist;
    }


    public static function driverselect(){
        $html = <<<EOT
        <select id='driverselect'>
EOT;

        /* foreach( self::installed_drivers() as $driver){
            $html .= "<option>{$driver}</option>";
        } */
        $drivers = self::installed_drivers();
        for($i=0; $i<count($drivers); $i++) 
            $html .= "<option value='{$i}'>{$drivers[$i]}</option>";

        $html .= <<<EOT
        </select>
EOT;

        return $html;
    }

}//end class