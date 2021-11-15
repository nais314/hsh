<?php


    class pageController {

        # add access-control (permission) rules,
        # according to used AC function (simple, rbac, custom)
        private static $ac_rules =

        [
            'common'=>[
                # 'action' => 'rule' | ['rules']
                //'*'=>'loggedin',
                //'*'=>['loggedin'],
                '*'=> 'loggedin',
            ],
            'lockdown'=>[
                '*'=>['deny'],
            ],
        ]

        ;

        public function beforeAction(){

            //bony\AC::allowLoggedin($return=false);

            bony\AC::check(self::$ac_rules);

            /* if(\app::$action != 'tree' && \app::$action != 'insert')
            {
                self::$menu = [
                    'functions' => [
                        'update'=>"?r=".\app::$module."/update&id={$_REQUEST['id']}",
                        'view'=>"?r=".\app::$module."/view&id={$_REQUEST['id']}",
                        'tree'=>"?r=".\app::$module."/tree",
                        'create'=>"?r=".\app::$module."/create",
                    ],
                ] + self::$menu;
            } */

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

        public function view($id=null){

            if($id == null) 
                if(!empty($_REQUEST['id'])){
                    $id = $_REQUEST['id'];
                }else{
                    return false;
                }

            ob_start();
            include(\app::$basepath.DS
                .'folders'.DS.$_SESSION['division']['folder'].DS.'pro'
                .DS.$id.'php'
                );
            $content = ob_get_clean();

            include \app::layout();
        }//---------------------------------------------------------------------


        public function show($id=null){

            if($id == null) 
                if(!empty($_REQUEST['id'])){
                    $id = $_REQUEST['id'];
                }else{
                    return false;
                }

            include(\app::$basepath.DS
                .'folders'.DS.$_SESSION['division']['folder'].DS.'pro'
                .DS.$id.'.php'
                );


        }//---------------------------------------------------------------------


    }//end class