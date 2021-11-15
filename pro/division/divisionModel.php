<?php
//use bony\ModelBase;
//include \app::$basepath.DIRECTORY_SEPARATOR.'pro'.DIRECTORY_SEPARATOR.'ModelBase.php';


    class divisionModel /* extends bony\ModelBase */ {
        public static $tableName = 'division';

        /**
        bony Validator properties ------------------------------------*/
        public static $ruleset;

        public static function beforeSave(&$row){
            if (is_object($row)) { # Row::save()
                $data = &$row->data;
            }else{ # resql::save()
                $data = &$row;
            }

            //if(isset($data['parent'])) $data['parent'] = (int)$data['parent'];
            if(isset($data['hostname']) and empty($data['hostname']))
                $data['hostname'] = null;

            \app::$logger->debug("beforeSave data: ".var_export($data,true));
        }

        public static function afterSave(&$row){
            if (is_object($row)) { # Row::save()
                $data = &$row->data;
            }else{ # resql::save()
                $data = &$row;
            }
            \app::$logger->debug("aftersave data: ".var_export($data,true));
            if(isset($data['parent']) && $data['parent'] != null && $data['parent'] != ""){
                $parent_table = (isset(self::$ruleset['properties']['parent']['tableName']))? self::$ruleset['properties']['parent']['tableName'] : self::$tableName;

                $parent_table = \bony\resql::fix_tablename($parent_table);

                $sql = "UPDATE $parent_table "
                ."SET has_child = 1 WHERE id = {$data['parent']}".WHERE_ROOTDIV;
                \app::$logger->debug($sql);
                \app::$pdo->exec($sql);

            }//parent

        return true;
        }//...............................................................

        /**
        Level-1 & II get_user_divisions($uid) ------------------------*/
        /* public function load_child_divisions($parent=null, &$array, $prefix='-', $level = 0){

            $result = app::$db->division()
            ->select("id as 'key', name as 'value'")
            ->where('parent', $parent)
            //->where('rootdiv', $_SESSION['division']['rootdiv'])
            ;
            $rows = $result->fetchAll();
            if(empty($rows)) return false;
            foreach($rows as $row) {
                $row['value'] = str_repeat ( $prefix , $level ) . $row['value'];
                array_push ( $array , $row->getData() );

                self::load_child_divisions($row['key'], $array, $prefix, $level +1);
            }
        } */



    }

    /**
    to add closures, functions, etc, (dynamic data), init must be separated */
    function __construct(){
            divisionModel::$ruleset = [

                'common' => [ #---scenario---
                    'name' => [ #---property---
                        'alphanumeric', # string: validator-function name (v_alphanumeric)
                        'minlength' => 3, # 'fun name' => params | [param, param]
                        # using non-assoc arrays was good for fast deserialization:
                        //['maxlength',80], # array: fun-name,  params | [param, param], error-message
                        'maxlength' => 80,



                        /* ['filter_test',  function(&$value){ # <name discarded>, <function(&<var>)>
                            $value = 'FILTERED';
                            return true;
                        }], */
                    ],//name ---property---

                    'hostname' => 'null', # emtpy string should be null otherwise unique constraint violation
                    'parent' => 'id', # null or number
                ],// common ---scenario---



                'insert' => [
                    'name' => [ #---property---
                        ['unique', function($value){ # <name discarded>, <function>
                            //TODO uique in Siblings only
                            $field = 'name' ;
                            //if(\app::$db->division()->where([$field=>$value])->rowCount()){
                            if(\bony\resql::count([$field=>$value],null,null)){
                                return "Name must be unique!";
                            }else{return true;}
                        }],
                    ],//name ---property---
                    'rootdiv' => $_SESSION['rootdiv']
                ],// insert ---scenario---




                # form input label's section:
                /**
                'visibility': public, private, readonly, masked */
                'properties' => [
                    'name' => [
                        'label'         => 'Name',
                        'visibility'    => 'public',
                        'display'       => 'text',
                        'required'      => true,
                        'autofocus'     => true,
                    ],
                    'default_action' => [
                        'label'         => 'Default Action',
                        'visibility'    => 'public',
                        'display'       => 'text',
                    ],
                    'hostname' => [
                        'label'         => 'Hostname',
                        'visibility'    => 'public',
                        'display'       => 'text',
                    ],
                    'folder' => [
                        'label'         => 'Folder',
                        'visibility'    => 'public',
                        'display'       => 'text',
                    ],
                    'scenario' => [
                        //'label'         => 'Hostname',
                        'visibility'    => 'public',
                        'display'       => 'text',
                        'datalist'      => ['normal', 'lockdown']
                    ],
                    /* 'has_child' => [
                        //'label'         => 'Hostname',
                        'visibility'    => 'readonly',
                        'display'       => 'text',
                    ], */
                    /* 'rootdiv' => [
                        'display'       => 'tree', //'select',
                        'tableName'     => 'division',
                        'visibility'    => 'private',
                    ], */
                    'parent' => [
                        'visibility'    => 'public',
                        'display'       => 'tree', //'select',
                        'tableName'     => 'division',
                        /* 'datalist'      => function(){
                            $array=[];
                            $result = app::$db->division()
                                ->select('name as value')
                                ->where('id', $_SESSION['division']['id']);
                            $rows = $result->fetchAll();
                            foreach($rows as $row) $array[]=$row->getData();
                            return $array;
                        }, */
                        /* 'optionlist'      => function(){
                            // $array=[];
                            // $result = app::$db->division()
                            //     ->select("id as 'key', name as 'value'")
                            //     ;
                            // $rows = $result->fetchAll();
                            // foreach($rows as $row) $array[]=$row->getData();
                            
                            $array=[];
                            divisionModel::load_child_divisions($parent=null, $array);
                            
                            return $array;
                        }, */
                        /* 'optionfilter'   => function(&$datas){
                            # ex: object level Access Control
                            foreach ($datas as &$data) {
                                $data['value'] = ''.$data['value'];
                            }
                        }, */
                    ], //end parent
                ],// end properties



            ];// end ruleset............................

    }
    __construct();
