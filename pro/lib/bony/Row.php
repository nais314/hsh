<?php namespace bony;
/**
 * CLASS to implement extra repeated functionality
 UNDO
 aftersave - use prev data
 beforesave load prev data?

 @return false on error
 */
class Row{
    public $data = []; // [column->data]

    public $modelClass = null;
    public $tableName = null;
    //public $id; // $data['id']
    //..............................................
    function __construct($data=null, /* $tableName=null, */ $model=null){

        if($model==null){
            if(null != (\app::$modelClass)) {
                $this->modelClass=\app::$modelClass;
            }else return false;
        }else{
            $this->modelClass = $model.'Model';
        }

        if(isset(($this->modelClass)::$tableName)){
            $this->tableName = ($this->modelClass)::$tableName;
        }else return false;



        //\app::$logger->debug(__METHOD__.':'.$this->tableName);
        //\app::$logger->debug(__METHOD__.':'.(\app::$module.'Model')::$tableName);

        //$this->modelClass  = (\app::$module.'Model')::$tableName;

        if($data != null){
            if(!\is_array($data)){ # id passed as argument
                $this->load($data);
            }else{
                $this->setData($data);
            }
        }



    }// end construct ______________________________________________

    public function __call ( $name , $arguments ){
        return new Row (
            $name,
            resql::load($name, $this[($name."_id")])
        );
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    //..............................................

    function setData(&$data){
        $this->data = $data;
    }
    function getData(){
        return $this->data;
    }




    function load($id = null){
        //\app::$logger->debug(__METHOD__."-".$this->tableName);
        $this->data = \bony\resql::load($id, $this->tableName);
        //\app::$logger->debug(__METHOD__.':'.var_export($this->data,true) );
        # save pre-state in session
        $_SESSION['undo'][$this->tableName][$id] = $data;
    }//_______________________________________________________________


    public static function select($id = null, $model = null){
        $row = null;
        if (!empty($model)) {
            $row = new \bony\Row(null, $model);
        }else{
            $row = new \bony\Row();
        }
        $row->load((int)$id); //?$_REQUEST['id']
        //\app::$logger->debug(__METHOD__."-".$row->tableName);

        //TODO # save pre-state in session
        //$_SESSION['undo'][$row->tableName][$id] = $row->data;
        return $row;
    }//_______________________________________________________________





    protected function bindValues(&$stmt){
        # bindValues
        foreach( $this->data as $key => $value){
            //\app::$logger->debug(__METHOD__.": ".$key." => ".$value);
            if(is_null($value)){
                $stmt->bindValue(":$key", $value, \PDO::PARAM_NULL);
            }elseif(is_int($value)){
                $stmt->bindValue(":$key", $value, \PDO::PARAM_INT);
            }else{
                $stmt->bindValue(":$key", $value, \PDO::PARAM_STR);
            }
        }

    }//_______________________________________________________________





    function insert(){
        if(\method_exists($this->modelClass, 'beforeSave'))
            $this->modelClass::beforeSave($this);

        if(!empty($this->data)){
            //\app::$logger->debug("insert begin > ".var_export($this->data,true));

            $validation_result = \bony\Validator::validate(
                $this->data,
                $this->modelClass::$ruleset,
                'insert');

                if($validation_result !== true) die("VALIDATION ERROR");
                //var_dump($this->data);

                if(!isset($this->data['rootdiv']) || empty($this->data['rootdiv'])) $this->data['rootdiv'] = $_SESSION['division']['rootdiv'];

                //\app::$logger->debug($this->data);

            $VALUES = [];

            foreach( array_keys($this->data) as $key){
                $VALUES[] = ":$key";
            }
            $VALUES = implode(', ', $VALUES);

            $sql =
            "INSERT INTO ".\bony\resql::fix_tableName($this->tableName)
            ."(".implode(',',array_keys($this->data)).")"
            ." VALUES ($VALUES)";
            //.WHERE_ROOTDIV

            $stmt = \app::$pdo->prepare($sql);

            # bindValues
            foreach( $this->data as $key => $value){

                if(is_null($value)){
                    $stmt->bindValue(":$key", $value, \PDO::PARAM_NULL);
                }elseif(is_int($value)){
                    $stmt->bindValue(":$key", $value, \PDO::PARAM_INT);
                }else{
                    $stmt->bindValue(":$key", $value, \PDO::PARAM_STR);
                }
                //\app::$logger->debug(__METHOD__.": ".$key."=>".$value);
            }
            //\app::$logger->debug(__METHOD__.": ".$sql);

            if(!$stmt->execute() ){
                $arr = $stmt->errorInfo();
                print_r($arr);
                \app::$logger->debug(__METHOD__.": ".var_export($arr,true)."\n".$sql);
            }else{
                $this->data['id'] = \app::$pdo->lastInsertId();
                if(is_callable($this->modelClass."::afterSave"))
                    $this->modelClass::afterSave($this);
                //\app::$logger->debug("aftersave is_callable> ".is_callable($this->modelClass."::afterSave"));
            }

        }

    }//_______________________________________________________________






    /**
     TODO: $this->data, $this->tableName
     */
    function save(){

        if(\method_exists($this->modelClass, 'beforeSave'))
            $this->modelClass::beforeSave($this);

        if(!empty($this->data)){
            $validation_result = \bony\Validator::validate(
                $this->data,
                $this->modelClass::$ruleset,
                'update');

            //\app::$logger->debug(__METHOD__.var_export($this->data,true));
            if($validation_result !== true)
                die("VALIDATION ERROR \n" . $validation_result);

            $SET = [];
            foreach( array_keys($this->data) as $key){
                if ($key == 'id') continue;
                $SET[] = "$key = :$key";
            }
            $SET = implode(', ', $SET);

            $sql = "UPDATE ".\bony\resql::fix_tableName($this->tableName)
                ." SET $SET"
                ." WHERE id = :id"
                .WHERE_ROOTDIV;

            //\app::$logger->debug($sql);

            $stmt = \app::$pdo->prepare($sql);

            $this->bindValues($stmt);

            if(!$stmt->execute()){
                $arr = $stmt->errorInfo();
                print_r($arr);
                \app::$logger->debug(__METHOD__."> ".$arr);
                //$stmt->debugDumpParams();
            }

            //\app::$logger->debug(__METHOD__."> ".var_export($this->data,true));

        }

        if(\method_exists( $this->modelClass, 'afterSave'))
            $this->modelClass::afterSave($this);

    }//_______________________________________________________________




}// end class