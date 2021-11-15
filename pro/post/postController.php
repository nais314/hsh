<?php

    function postview_accesrule($value, $param, $msg){
        $row = \bony\Row::select((int)$_REQUEST['id'], 'post');
        //$row = new \bony\Row();
        //$row->load((int)$_REQUEST['id']);
        //\app::$logger->debug(var_export($row,true));
        return ($row->is_public == 1) or \bony\AccessValidators::v_loggedin(null,null);
    }

    class postController {

        # add access-control (permission) rules,
        # according to used AC function (simple, rbac, custom)
        private static $ac_rules =

        [
            'common'=>[
                # 'action' => 'rule' | ['rules']
                //'*'=>'loggedin',
                //'*'=>['loggedin'],
                '*'=> 'postview_accesrule',
                'update'=>'token',
                'update2'=>'token',
                'create'=>'token',
                'create2'=>'token',
            ],
            'lockdown'=>[
                '*'=>['deny'],
            ],
        ]

        ;

        public function beforeAction(){

            //bony\AC::allowLoggedin($return=false);

            bony\AC::check(self::$ac_rules);

            if(\app::$action != 'tree' && \app::$action != 'insert')
            {
                self::$menu = [
                    'functions' => [
                        'update'=>"?r=".\app::$module."/update&id={$_REQUEST['id']}",
                        'view'=>"?r=".\app::$module."/view&id={$_REQUEST['id']}",
                        'tree'=>"?r=".\app::$module."/tree",
                        'create'=>"?r=".\app::$module."/create",
                    ],
                ] + self::$menu;
            }

        }



        /********************************************************************* */
        public function install(){

        }
        /********************************************************************* */
        /**
         TODO
         [*]
         ['view']
         */
        private static $menu = [
            /* "dummy1" => [
                "title3" => "",
                "www.314.hu",
                "gombaaaa"
            ],
            "dummy2" => [
                "title3" => "",
                "www.314.hu",
                "gombaaaa",
                "SUB 4444" => [
                    "title3" => "",
                    "www.314.hu",
                    "gombaaaa"
                ]
            ], */
        ];

        /********************************************************************* */

        /**
        $tableName = null, //\app::$module,
        $parent = null, // load from this down
        $template = '_treeitem.php',
        $loader = null // loader php function >> tree_root.dataset.loader
        */
        public function tree(){

            $content = \bony\Treeview::treeview();

            include \app::layout();
        }//----------------------------------------------------------------------


        public function view($id=null){

            if($id == null) 
                if(!empty($_REQUEST['id'])){
                    $id = $_REQUEST['id'];
                }else{
                    return false;
                }

            $data = \bony\resql::load($id);

            $content = "<h2>{$data['name']}</h2>{$data['content']}";

            include \app::layout();
        }//----------------------------------------------------------------------

        //TODO REWRITE
        public function ajaxview($id=null){

            if($id == null) 
                if(!empty($_REQUEST['id'])){
                    $id = $_REQUEST['id'];
                }else{
                    return false;
                }


            $data = \bony\resql::load($id);

            $content = "<h2>{$data['name']}</h2>{$data['content']}";

            //TODO
            $ovid = \app::genid(5)."_view";
            $content = "<div id='$ovid' style='background-color:white' onclick='event.preventDefault();event.stopPropagation();'>".$content."</div>";

            echo $content;
        }//---------------------------------------------------------------------






        public function update($id=null){ // ROW insert test

            if($id == null) $id = $_REQUEST['id'];

            if(isset($_POST[\app::$module])){
                $obj = new \bony\Row($data=$_POST[\app::$module]);
                $obj->id = $id;

                $obj->save();

                \app::redirect("?r=".\app::$module."/view&id=".$obj->id);
            }

            
            $obj = \bony\Row::select($id);
            //$obj->load($id);
            $content = bony\Former::make_form($obj->data);
            //$content .= var_export($obj->data, true);

            include \app::layout();


        }//------------------------------------------------------------


        public function create(){ // ROW insert test
            if(isset($_POST[\app::$module])){
                $obj = new \bony\Row($_POST[\app::$module]);

                $obj->insert();

                \app::redirect("?r=post/view&id=".$obj->id);
            }


            $content = bony\Former::make_form();

            include \app::layout();


        }//------------------------------------------------------------



        /**
        render is good for xhttp request
         */
        public static function browse($id=null, $render=false){
            if(isset($_POST[\app::$module])){

            }



            $rows = \bony\resql::fetchAll(['parent'=>$parent], null,
                                            \app::$tableName,
                                            $columns='*',
                                            $reassoc=false);

            $content = var_export($rows,true);



            if($render){
                echo $content;
            }else{
                include \app::layout();
            }
        }


        /**
         * TODO SEARCH post - generate from FORMER
         */









        public function drop(){
            \app::$logger->debug(__METHOD__.": ".var_export($_REQUEST,true));
            # custom handlers

            # built in handler - handles parent-child relations
            //\bony\dropController::defaultDropHandler();

        }//---------------------------------------------------------------------












        public function update2($id=null){ // ROW insert test

            if($id == null) $id = $_REQUEST['id'];

            if(isset($_POST[\app::$module])){

                //\bony\resql::save();
                \bony\resql::save(
                    $id=$id,
                    $data=$_POST[\app::$module],
                    $tableName= \app::$tableName,
                    $modelClass= \app::$modelClass );

                //\app::redirect("?r=post/view&id=".$id);
                self::view($id);
                return true;
            }

            $data = \bony\resql::load($id);

            $content = bony\Former::make_form($data);

            include \app::layout();

        }//---------------------------------------------------------------------


        public function create2(){ // ROW insert test

            if(isset($_POST[\app::$module])){
                $obj = new \bony\Row($_POST[\app::$module]);

                $obj->insert();

                \app::redirect("?r=post/view&id=".$obj->id);
                die('OK');
            }


            $obj = new \bony\Row(); //auto param: \app::$module

            $obj->load($id);

            $content = bony\Former::make_form(($obj->data));

            $content .= var_export($obj->data, true);

            include \app::layout();


        }//---------------------------------------------------------------------







        /**
         TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT
         EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
         SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
         TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT
         */
        public function test($id = 1){
            $t1 = \bony\Treeview::treeview();
            $t2 = \bony\Treeview::treeview();
            $content = <<<EOT
                <style> td {padding: .5em} </style><br>
                <table><tr><td>$t1</td><td>$t2</td></tr>
EOT;

            include \app::layout();

        }




        /**
         * test old
         */
        public function test2($id = 2){

            if (isset($_REQUEST['id'])) $id = $_REQUEST['id'];

            if(!empty($id)){
                $data = \bony\resql::load($id);
            }

            if(isset($_POST[\app::$module])){
                $validation_result = bony\Validator::validate(
                    $_POST[\app::$module],
                    \app::$modelClass::$ruleset,
                    'insert');

                var_dump($_POST[\app::$module]);
                echo "<br>";
                /* var_dump($validation_result);
                echo "<br>";
                echo $_POST[\app::$module]['date_create'];
                echo "<br>";

                die(); */

                if($validation_result !== true) die("VALIDATION ERROR");

                $SET = [];
                /* foreach( array_keys($_POST[\app::$module]) as $key){
                    $SET[] = "$key = :$key";
                }
                $SET = implode(', ', $SET); */
                foreach( array_keys($_POST[\app::$module]) as $key){
                    $SET[] = ":$key";
                }
                $SET = implode(', ', $SET);

                $stmt = app::$pdo->prepare(
                /* "UPDATE ".\bony\resql::fix_tableName(\app::$module)
                ." SET $SET"
                ." WHERE id = :id"
                .WHERE_ROOTDIV
                ); */
                "INSERT INTO ".\bony\resql::fix_tableName(\app::$tableName)
                ."(".implode(',',array_keys($_POST[\app::$module])).")"
                ." VALUES ($SET)"
                //.WHERE_ROOTDIV
                );
                if(!$stmt->execute($_POST[\app::$module]) ){
                    $arr = $stmt->errorInfo();
                    print_r($arr);
                };



                die();
            }

            /* $row = \app::$db->division($id);
            $data = $row->getData();
            //$content = var_export($data, true);


            $validation_result = bony\Validator::validate(
                $data,
                (\app::$module.'Model')::$ruleset,
                'load');
            */

            //$content = var_export($validation_result, true);
            # function make_form($tableName, &$data, $enableSave = true)
            //$data = ['parent' => null];
            $content = bony\Former::make_form($data);


            include \app::layout();


        }





        public function test4(){
            $modelClass = \app::$module.'Model';
            //\app::$arr_JS['ckeditor'] = "ckeditor/ckeditor.js";



            if(isset($_POST[\app::$module])){
                $validation_result = bony\Validator::validate(
                    $_POST[\app::$module],
                    $modelClass::$ruleset,
                    'insert');


                if($validation_result !== true) die("VALIDATION ERROR");

                $VALUES = [];

                foreach( array_keys($_POST[\app::$module]) as $key){
                    $VALUES[] = ":$key";
                }
                $VALUES = implode(', ', $VALUES);

                $stmt = app::$pdo->prepare(

                "INSERT INTO ".\bony\resql::fix_tableName(\app::$tableName)
                ."(".implode(',',array_keys($_POST[\app::$module])).")"
                ." VALUES ($VALUES)"
                //.WHERE_ROOTDIV
                );
                if(!$stmt->execute($_POST[\app::$module]) ){
                    $arr = $stmt->errorInfo();
                    print_r($arr);
                };


                (\app::$module.'Model')::afterSave($_POST[\app::$module]);



                die();
            }


            $content = bony\Former::make_form($data);


            include \app::layout();


        }


    }//end class