<?php

    class pageModel {
        public static $tableName = 'page';

        public static $rel8_path = ''; //TODO to remember

        /**
        bony Validator properties ------------------------------------*/
        public static $ruleset;


        public static function beforeSave(&$row){
            if (is_object($row)) { # Row::save()
                $data = &$row->data;
            }else{ # resql::save()
                $data = &$row;
            }

            \bony\resql::save_undo($data['id'], \app::$tableName);

            /* # save undo data...............
            $sql = "SELECT COUNT(*) FROM ".\app::$tableName."_undo WHERE id = ?";
            $stmt = \app::$pdo->prepare($sql);
            $stmt->execute([$data['id']]);
            if($stmt->fetchColumn() > 0){
                $sql = "DELETE FROM ".\app::$tableName."_undo WHERE id = ?";
                $stmt = \app::$pdo->prepare($sql);
                $stmt->execute([$data['id']]);
            }
            $sql = "INSERT INTO ".\app::$tableName."_undo "
                  ." SELECT * FROM ".\app::$tableName
                  ." WHERE id = ?";
            $stmt = \app::$pdo->prepare($sql);
            $stmt->execute([$data['id']]);
            //............................. */

        }


        public static function afterSave(&$row){
            if (is_object($row)) { # Row::save()
                $data = &$row->data;
            }else{ # resql::save()
                $data = &$row;
            }
            //\app::$logger->debug("aftersave data: ".var_export($data,true));
            # update current parent if any
            
            if(isset($data['parent']) && $data['parent'] != null && $data['parent'] != ""){
                /* $parent_table = (isset(self::$ruleset['properties']['parent']['tableName']))? self::$ruleset['properties']['parent']['tableName'] : self::$tableName;
                $parent_table = \bony\resql::fix_tablename($parent_table); */

                $sql = "UPDATE ".\bony\resql::fix_tablename(\app::$tableName)
                ." SET has_child = 1 WHERE id = ? ".WHERE_ROOTDIV;
                $stmt = \app::$pdo->prepare($sql);
                $stmt->execute([$data['parent']]);

            }//parent   

            # update prev parents has_child
            /* $sql = "SELECT parent FROM ".\bony\resql::fix_tablename(\app::$tableName.'_undo')
                ." WHERE id = ?".WHERE_ROOTDIV;
            $stmt->execute([$data['id']]);
            $undo = $stmt->fetch(PDO::FETCH_ASSOC); */
            $undo = \bony\resql::load_undo($data['id'], $columns='parent');
            \app::$logger->debug(__METHOD__."undo: ".var_export($undo,true).var_export($data,true));

            if($undo['parent'] != $data['parent']){
                \bony\resql::recalc_has_child($undo['parent'], $tableName);
                /* # check prev parent if still has child
                $sql = "SELECT COUNT(*) FROM ".\bony\resql::fix_tablename(\app::$tableName)
                    ." WHERE parent = ?".WHERE_ROOTDIV;
                $stmt = \app::$pdo->prepare($sql);
                $stmt->execute([$undo['parent']]);
                $number_of_rows = $stmt->fetchColumn();
                if($number_of_rows == 0){
                    $sql = "UPDATE ".\bony\resql::fix_tablename(\app::$tableName)
                        ." SET has_child = 0 WHERE id = ? ".WHERE_ROOTDIV;
                    $stmt = \app::$pdo->prepare($sql);
                    $stmt->execute([$undo['parent']]);
                }
                \app::$logger->debug(__METHOD__." number_of_rows:  ".$number_of_rows); */
            }


        return true;
        }//...............................................................


    }//******************************************************************** */

    /**
    to add closures, functions, etc, (dynamic data), init must be separated */
    function __construct(){
        pageModel::$ruleset = [

                'common' => [ #---scenario---
                    'name' => [ #---property---
                        'alphanumeric', # string: validator-function name (v_alphanumeric)
                        'minlength' => 4, # 'fun name' => params | [param, param]
                        # using non-assoc arrays was good for fast deserialization:
                        //['maxlength',80], # array: fun-name,  params | [param, param], error-message
                        'maxlength' => 256,
                    ],//name ---property---

                    'date_modify' => 'now',
                    'rootdiv' => $_SESSION['user']['rootdiv'],
                    'parent' => 'id',
                    'id'    => 'id',
                    
                ],// common ---scenario---



                'insert' => [
                    /* 'name' => [ #---property---
                        ['unique', function($value){ # <name discarded>, <function>
                            $field = 'name' ;
                            if(\app::$db->division()->where([$field=>$value])->rowCount()){
                                return "Name must be unique!";
                            }else{return true;}
                        }],
                    ],//name ---property--- */
                    'date_create' => 'now',

                ],// insert ---scenario---




                # form input label's section:
                /**
                'visibility': public, private, readonly, masked */
                'properties' => [
                    'name' => [
                        'label'         => 'Title',
                        'visibility'    => 'public',
                        'display'       => 'text',
                        'required'      => true,
                        'autofocus'     => true,
                    ],
                    'content' => [
                        'label'         => 'Content',
                        'visibility'    => 'public',
                        'display'       => 'ckeditor',
                        //'style'         => 'flex-basis: 100%', /* TODO */
                        'attributes'    => 'rows=30',
                        'flex-basis'    => '100%' /* TODO */
                    ],
                    'tags' => [
                        'label'         => 'tags',
                        'visibility'    => 'public',
                        'display'       => 'text',
                    ],
                    
                    'parent' => [
                        'visibility'    => 'public',
                        'display'       => 'tree', //'select',
                        'onchange'      => "alert('changed');",
                        'tableName'     => 'page',
                        'id'
                        
                    ], //end parent

                    'is_public' => [
                        'label'         => 'Public?',
                        'display'       => 'checkbox-large', /* TODO */
                        'visibility'    => 'public',
                        'flex-basis'    => '10%', 
                    ],
                    'date_create' => [
                        'label'         => 'Date Created',
                        'visibility'    => 'readonly',
                        'display'       => 'text',
                        'flex-basis'    => '20%', 
                    ],
                    'date_modify' => [
                        'label'         => 'Date Modified',
                        'visibility'    => 'readonly',
                        'display'       => 'text',
                        'flex-basis'    => '20%', 
                    ],
                    'id' => [
                        'visibility'    => 'hidden',
                        'id'
                    ],

                    'files' => [
                        'display'       => 'files',
                        'visibility'    => 'public',
                    ]

                    # TREE:
                    /* 'has_child' => [
                        //'label'         => 'Hostname',
                        'visibility'    => 'readonly',
                        'display'       => 'text',
                    ], */

                    # DIV
                    
                ],// end properties



            ];// end ruleset............................




    }
    __construct();
