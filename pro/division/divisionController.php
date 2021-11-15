<?php

    class divisionController {

        # add access-control (permission) rules,
        # according to used AC function (simple, rbac, custom)
        private static $ac_rules = 

        [
            'common'=>[
                # 'action' => 'rule' | ['rules']
                '*'=>'loggedin',
                //'*'=>['loggedin'],
            ],
            'lockdown'=>[
                '*'=>['deny'],
            ],            
        ]
        
        ;

        public function beforeAction(){

            bony\AC::allowLoggedin($return=false);

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




        public function install(){
            
        }


        /********************************************************************* */
        
        private static $menu = [
            /* "harmadik" => [
                "title3" => "",
                "www.314.hu",
                "gombaaaa"
            ],
            "4gyedik" => [
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
        


        public function view($id = 0){

            if($id == null) 
                if(!empty($_REQUEST['id'])){
                    $id = $_REQUEST['id'];
                }else{
                    return false;
                }

            //!TEST
            $data = \bony\resql::fetch(['id'=>$id], null, app::$module);

            $pub_data = [];
            foreach($data as $key => &$value){
                if(divisionModel::$ruleset['properties'][$key]['visibility'] == 'public')
                $pub_data[$key]=$value;
            }


            $content = "<h2>".\app::$module."</h2>"
            ."<h3>".\app::$action."</h3>"
            ;//."<pre>" .var_export($pub_data,true). "</pre>";
            $content .= "<table class='info'>";
            foreach($pub_data as $key=>$value){
                if($key == 'parent' and !empty($value)){
                    $value = \bony\resql::fetch(['id'=>$value], null, app::$module, $columns='name')['name'];
                }
                $content .= "<tr><td>{$key}</td><td>{$value}</td></tr>";
            }
            $content .= "</table>";
		
		    //include \app::$basepath.'/pro/view/layout.php';
            include \app::layout();
        }//----------------------------------------------------------------------





        public function view2($id = 0){

            if($id == null) 
                if(!empty($_REQUEST['id'])){
                    $id = $_REQUEST['id'];
                }else{
                    return false;
                }


            //!TEST
            $obj = \bony\Row::select($id);

            $pub_data = [];
            foreach($obj->getData() as $key => &$value){
                if(divisionModel::$ruleset['properties'][$key]['visibility'] == 'public')
                $pub_data[$key]=$value;
            }


            $content = "<h2>".\app::$module."</h2>"
            ."<h3>".\app::$action."</h3>"
            ;
            $content .= "<table>";
            foreach($pub_data as $key=>$value){
                if($key == 'parent' and !empty($value)){
                    $value = \bony\resql::fetch(['id'=>$value], null, app::$module, $columns='name')['name'];
                }
                $content .= "<tr><td>{$key}</td><td>{$value}</td></tr>";
            }
            $content .= "</table>";
		
		    //include \app::$basepath.'/pro/view/layout.php';
            include \app::layout();
        }//----------------------------------------------------------------------








        public function create(){

            \app::$logger->log('DEBUG',__FUNCTION__);

            if(isset($_POST[\app::$module])){
                $obj = new \bony\Row($_POST[\app::$module]);

                $obj->insert();

                \app::redirect("?r=".\app::$module."/view&id=".$obj->id);
            }


            $content = bony\Former::make_form();

            include \app::layout();


         
/* 
            if(isset($_POST[divisionModel::$tableName])){

                $validation_result = bony\Validator::validate($_POST['division'], divisionModel::$ruleset, $scenario='insert') ;

                if($validation_result === true){
                    //$row = \app::$db->createRow( 'division', $properties = $_POST['division'] );
                    $row = \bony\resql::insert($_POST['division']);

                    $content = var_export($row,true);
                }else {
                    $content = var_export($validation_result, true);
                }
            }else{
                $content = \app::render('_form');
            }

            
            include \app::layout(); */
        }//----------------------------------------------------------------------



        public function update($id = 0){

            \app::$logger->log('DEBUG',__FUNCTION__);

            if($id == null) $id = $_REQUEST['id'];

            if(isset($_POST[\app::$module])){
                $obj = new \bony\Row($data=$_POST[\app::$module]);
                $obj->id = $id;

                $obj->save();

                \app::redirect("?r=".\app::$module."/view&id=".$obj->id);
            }

            $obj = \bony\Row::select($id);

            $content = bony\Former::make_form($obj->data);

            //$content .= var_export($obj->data, true);

            include \app::layout();
        }//----------------------------------------------------------------------



        /** 
        $tableName = null, //\app::$module,
        $parent = null, // load from this down
        $template = '_treeitem.php',
        $loader = null // loader php function >> tree_root.dataset.loader
        */
        public function tree(){
            $content = \bony\Treeview::treeview(
                $module = app::$module,
                $parent = null, // load childs recursively
                $template = '_treeitem.php', // in pro/{tableName}/view/
                $droptarget = null,
                $action = 'vieworedit');

            $content .=<<<EOT
<script>
function vieworedit(event, ti_selfid, treeid){
    window.location.href="?r=division/view&id=" + event.target.dataset.id;
}
</script>
EOT;

            include \app::layout();
        }//----------------------------------------------------------------------





    }//EON =====================================================================