<?php namespace bony;

class resql{

    static function fix_tableName($tableName){
        return TBPREFIX.$tableName.TBPOSTFIX;
    }//_______________________________________________________________

/* 
    static function in($arr){
        $str = " IN (";
        $arr_len = count($arr);
        for($i = 0; $i < $arr_len -1; $i++){
            $str .= $arr[$i];
            if( $i < $arr_len -1) $str .= ',';
        }

        return $str;
    }//_______________________________________________________________
 */

    static function load($id, $tableName=null){
 
        if(empty($tableName)) $tableName = \app::$tableName;

        $tableName = self::fix_tableName($tableName);
        //\app::$logger->debug(__METHOD__." ".$id." ".$tableName);

        $stmt = \app::$pdo->prepare("SELECT * FROM $tableName WHERE id = ? ".WHERE_ROOTDIV);
        $stmt->bindParam(1, $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }//_______________________________________________________________







    /**
     * ... and than i tought, if i will something extra,
     * i will drop in a string SQL query string,
     * so this function can remain clean.
     * I think it is easyer to learn SQL than ORM...
     */
    static function find_stmt($wherestr, $var_arr, $tableName, $columns='*'){
        #resolve tableName
        if(empty($tableName)) $tableName = \app::$tableName;

        # add pre-post-fixes; handle multiple []
        if(is_string($tableName)){
            $tableName = self::fix_tableName($tableName);
        }elseif(is_array($tableName)){
            foreach($tableName as &$tname){
                self::fix_tableName($tname);
            }
            $tableName = \implode(",", $tableName);
        }

        /*FIX*/if(is_string($var_arr)) $var_arr = array($var_arr);
        /*FIX*/if($var_arr === null) $var_arr = [];

        # ['name' => 'Test Name']
        if(is_array($wherestr)){
            $WHERE = [];
            foreach( array_keys($wherestr) as $key){
                if(is_int($key)){
                    $WHERE[] = $wherestr[$key];
                }else
                if($wherestr[$key] == null
                || (isset($var_arr) && isset($var_arr[$key]) && $var_arr[$key] == null)
                ){
                    $WHERE[] = " $key IS NULL ";

                }elseif (is_array($wherestr[$key])) {
                    $WHERE[] = " $key IN("
                        .implode(',',$wherestr[$key])
                        .')';
                
                }else{
                    $WHERE[] = "$key = :$key";
                    # LOGICAL ERROR IF BOTH SET!!!
                    if(!isset($var_arr[$key])) $var_arr[$key] = $wherestr[$key];
                }
                
            }
            $wherestr = implode(' AND ', $WHERE);
        }

        if(defined('WHERE_ROOTDIV')) $wherestr.= WHERE_ROOTDIV;
        \app::$logger->debug(__METHOD__." SELECT $columns FROM $tableName WHERE $wherestr");
        $stmt = \app::$pdo->prepare("SELECT $columns FROM $tableName WHERE $wherestr");
        if(empty($stmt)){
            die('STMT ERROR');
        }

        return array($stmt, $var_arr);

        /* if(!empty($var_arr)) { 
            //\app::$logger->debug(__METHOD__."  ".var_export($var_arr,true));
            $stmt->execute($var_arr);
        }else{
            $stmt->execute();
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC); */
    }//_______________________________________________________________
    /* static function findit($wherestr, $var_arr, $tableName, $columns='*'){
        return current(self::find($wherestr, $var_arr, $tableName, $columns));
    }//_______________________________________________________________
 */

    public static function count($wherestr, $var_arr = null, $tableName){
        list($stmt, $var_arr)=self::find_stmt($wherestr, $var_arr, $tableName, $columns='COUNT(*)');
        if($stmt)
            if(!empty($var_arr)) { 
                //\app::$logger->debug(__METHOD__."  ".var_export($var_arr,true));
                $stmt->execute($var_arr);
            }else{
                $stmt->execute();
            }
        //return $stmt->fetch(\PDO::FETCH_ASSOC);
        return $stmt->fetchColumn();
    }//_______________________________________________________________


    static function fetch($wherestr, $var_arr, $tableName, $columns='*'){
        list($stmt, $var_arr)=self::find_stmt($wherestr, $var_arr, $tableName, $columns='*');
        \app::$logger->debug(__METHOD__."  ".var_export($var_arr,true));
        if($stmt)
            if(!empty($var_arr)) { 
                \app::$logger->debug(__METHOD__." - not empty var_arr ");
                $result = $stmt->execute($var_arr);
            }else{
                \app::$logger->debug(__METHOD__." - empty var_arr ");
                $result = $stmt->execute();
            }
        if($result){
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }else{ \app::$logger->debug(__METHOD__." - NO RESULT - "); return false; }
    }//_______________________________________________________________

    static function fetchAll($wherestr, $var_arr, $tableName, $columns='*', $reassoc=false){
        list($stmt, $var_arr)=self::find_stmt($wherestr, $var_arr, $tableName, $columns='*');
        if(!empty($var_arr)) { 
            //\app::$logger->debug(__METHOD__."  ".var_export($var_arr,true));
            $stmt->execute($var_arr);
        }else{
            $stmt->execute();
        }
        if($reassoc){
            $arr = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return self::reassoc($arr);
        }else{
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }//_______________________________________________________________


     
    /**
     TODO test, finish
     */
    function save(&$data=null, $tableName=null, $module=null, $validate=true){

        if(!empty($module)){
            $modelName = $module.'Model';
        }elseif(isset($_REQUEST) && isset($_REQUEST['module'])){
            $modelName = $_REQUEST['module'].'Model';
        }else $modelName = \app::$moduleClass;
        //\app::$logger->debug(__METHOD__."  ".$tableName."\n" );
        if(empty($tableName)){
            if(isset($_REQUEST) && isset($_REQUEST['tableName'])){
                $tableName = $_REQUEST['tableName'];
            }elseif(isset($modelName::$tableName)) { 
                $tableName = $modelName::$tableName;
            }else $tableName = \app::$module; // AJAX FAIL
        }

        //if($id == null) $id = $_REQUEST[$tableName]['id'];
        if($data == null) $data = $_REQUEST[$tableName];

        //\app::$logger->debug(__METHOD__.var_export($_REQUEST,true) );

        if(!empty($data)){
            if($validate){
                $validation_result = \bony\Validator::validate(
                    $data,
                    $modelName::$ruleset,
                    'update');

                if($validation_result !== true) return false; //die("VALIDATION ERROR" . $validation_result);
            }

            $SET = [];
            foreach( array_keys($data) as $key){
                if($key == 'id') continue;
                $SET[] = "$key = :$key";
            }
            $SET = implode(', ', $SET);

            $stmt = \app::$pdo->prepare(
                "UPDATE ".\bony\resql::fix_tableName($tableName)
                ." SET $SET"
                ." WHERE id = :id"
                .WHERE_ROOTDIV
            );

            self::bindValues($stmt, $data);
            \app::$logger->debug(__METHOD__." ".var_export($data,true));
            //\app::$logger->debug(var_export($_REQUEST[$modelName::$tableName],true));
            return $stmt->execute();
        }else{
            return false;
        }

    }//_______________________________________________________________

        /**
     TODO test, finish
     */
    function insert(&$data, $tableName=null, $modelName=null){
        $modelName = ($modelName)? $modelName : \app::$module.'Model';
        //$modelName = ($modelName)? $modelName : (\app::$module.'Model')::$tableName;
        //$tableName = ($tableName)? $tableName : $modelName::$tableName;
        if(empty($tableName)){
            if(isset($_POST) && isset($_POST['tableName'])){
                $tableName = $_POST['tableName'];
            }elseif(isset($modelName::$tableName)) { 
                $tableName = $modelName::$tableName;
            }else $tableName = \app::$module;
        }

        if(empty($data)) {
            if(!empty($_POST[$tableName])){
                $data=$_POST[$tableName];
            }else return false;
        }
        else/* if(!empty($data)) */{
            $validation_result = \bony\Validator::validate(
                $tableName,
                $modelName::$ruleset,
                'update');

            if($validation_result !== true) die("VALIDATION ERROR" . $validation_result);

            $data['rootdiv'] = $_SESSION['division']['rootdiv'];

            $VALUES = [];

            foreach( array_keys($data) as $key){
                $VALUES[] = ":$key";
            }
            $VALUES = implode(', ', $VALUES);

            $stmt = app::$pdo->prepare(
            "INSERT INTO ".\bony\resql::fix_tableName(\app::$module)
            ."(".implode(',',array_keys($data)).")"
            ." VALUES ($VALUES)"
            );
            if(!$stmt->execute($data) ){
                $arr = $stmt->errorInfo();
                #print_r($arr);
                \app::$logger->error(__METHOD__. ": ".$arr);
            };

            //!TEST
            if(\method_exists( $modelName, 'afterSave')) $modelName::afterSave($data);
        }
    }//_______________________________________________________________






     
    /**
     TODO test, finish
     */
    function save_undo(&$data=null, $tableName=null, $modelName=null){
        if(empty($tableName)) $tableName = \app::$tableName;

        if($tableName == null) $tableName = \app::$tableName;
        $undoTable = \bony\resql::fix_tablename($tableName.'_undo');
        $fixedTableName = \bony\resql::fix_tablename(\app::$tableName);

        # save undo data...............
        #   undo exists?
        $sql = "SELECT COUNT(*) FROM {$undoTable} WHERE id = ?";
        $stmt = \app::$pdo->prepare($sql);
        $stmt->execute([$data['id']]);
        if($stmt->fetchColumn() > 0){
            $sql = "DELETE FROM {$undoTable} WHERE id = ?";
            $stmt = \app::$pdo->prepare($sql);
            $stmt->execute([$data['id']]);
        }
        #   copy row to undo table
        $sql = "INSERT INTO {$undoTable} "
                ." SELECT * FROM ".$fixedTableName
                ." WHERE id = ?";
        $stmt = \app::$pdo->prepare($sql);
        $stmt->execute([$data['id']]);
        //.............................

    }//_______________________________________________________________

    function load_undo($id, $columns='*', $tableName=null){
        if(empty($tableName)) $tableName = \app::$tableName;

        if($tableName == null) $tableName = \app::$tableName;
        $undoTable = \bony\resql::fix_tablename($tableName.'_undo');
        //$fixedTableName = \bony\resql::fix_tablename(\app::$tableName);
        //.......

        $sql = "SELECT {$columns} FROM ".$undoTable
              ." WHERE id = ?";
        $stmt = \app::$pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }//_______________________________________________________________

    /**
     * checks and sets if treenode has child
     */
    function recalc_has_child($id, $tableName=null){
        if(empty($tableName)) $tableName = \app::$tableName;

        # check prev parent if still has child
        $sql = "SELECT COUNT(*) FROM ".\bony\resql::fix_tablename($tableName)
            ." WHERE parent = ?".WHERE_ROOTDIV;
        $stmt = \app::$pdo->prepare($sql);
        $stmt->execute([$id]);
        $number_of_rows = $stmt->fetchColumn();

        if($number_of_rows == 0){
            $sql = "UPDATE ".\bony\resql::fix_tablename($tableName)
                ." SET has_child = 0 WHERE id = ? ".WHERE_ROOTDIV;
            $stmt = \app::$pdo->prepare($sql);
            $stmt->execute([$id]);
        }
        \app::$logger->debug(__METHOD__." number_of_rows:  ".$number_of_rows);
    }//_______________________________________________________________





    protected function bindValues(&$stmt, &$data){
        # bindValues
        //\app::$logger->debug(__METHOD__." ".var_export($data,true));
        foreach( $data as $key => $value){
            //\app::$logger->debug("$key => $value");
            //\app::$logger->debug(__METHOD__.is_int($key));
            if(is_int($key)){
                if(is_null($value)){
                    $stmt->bindValue($key+1, $value, \PDO::PARAM_NULL);
                }elseif(is_int($value)){
                    $stmt->bindValue($key+1, $value, \PDO::PARAM_INT);
                }else{
                    $stmt->bindValue($key+1, $value, \PDO::PARAM_STR);
                }
            }else{
                //\app::$logger->debug(__METHOD__." $key => $value");
                if(is_null($value)){
                    $stmt->bindValue(":$key", $value, \PDO::PARAM_NULL);
                }elseif(is_int($value)){
                    $stmt->bindValue(":$key", $value, \PDO::PARAM_INT);
                    //\app::$logger->debug(__METHOD__." $key => $value");
                }else{
                    $stmt->bindValue(":$key", $value, \PDO::PARAM_STR);
                }
            }
        }

    }//_______________________________________________________________



    
    public function reassoc($data){
        $arr = [];
        foreach($data as $row){
            $arr[$row['id']]=$row;
            unset($arr[$row['id']]['id']);
        }
        return $arr;

    }//_______________________________________________________________


    /* public function resolve_TableName(&$tableName){
        if(isset($_POST) && isset($_POST['tableName'])){
            $tableName = $_POST['tableName'];
            return true;
        }

        $modelName = ($_POST['modelName'])? $_POST['modelName'] : \app::$module.'Model';
        if(isset($modelName::$tableName)) { 
            $tableName = $modelName::$tableName;
            return true;
        }
        
        $tableName = \app::$module;
    } */
//end class
}
