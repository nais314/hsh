<?php namespace bony;
class Treeview {

    /**
     move to TreeController????
     */


    public static function gen_treeitems(
        $tableName = null, //\app::$module,
        $parent = null, // PARENT sql ID or null - load from this down
        $template = '_treeitem.php'
        //$loader = null // loader php function >> tree_root.dataset.loader
    ){

        // resolve templatefile like an autoloader:
        if($template == null) $template = '_treeitem.php';
        if(stripos($template, ".php") == -1) $template = $template.'php';
        if(file_exists($template)){
        }elseif(file_exists('pro/'.$tableName.DS.'view'.DS.$template)){
            $template = 'pro/'.$tableName.DS.'view'.DS.$template;
        }elseif (file_exists(\app::$modulepath.DS.'view'.DS.$template)) {
            $template = \app::$modulepath.DS.'view'.DS.$template;
        }elseif (file_exists('pro/'.$dir.DS.'view'.DS.$template)) {
            $template = 'pro/'.$dir.DS.'view'.DS.$template;
        }
        // get Rows
        /* $result = \app::$db->$tableName()
        ->where('parent', $parent);
        $rows = $result->fetchAll(); */

        $rows = \bony\resql::fetchAll(['parent'=>$parent], null,
                                        $tableName,
                                        $columns='*', $reassoc=false);





        // foreach rows as data inject templatefile.....................
        \ob_start();
        $level = (isset($_REQUEST['tree']['level']))? $_REQUEST['tree']['level']+1 : 1;
        $css_class =  ($level > 1)? (($level % 2 == 0)? 'iodd':'ieven') : "";
        foreach($rows as $data){
            $data['level'] = $level;
            $data['css_class'] = $css_class;
            include($template);
            // CHILDS 
            echo "<div id='treeitem_childs-{$data['id']}' class='treeitem_childs' style=''></div>"; 
        }
        $return .= ob_get_clean();

        return $return;


    }
    //------------------------------------------------------


    public static function add_new_btn(){
        
    }



    /**
       MAIN ------------------------------------------------
       from a simple "tableNameController" class it can be called without parameters
     */
    public static function treeview(
        $module = null, // default: \app::$module,
        $tableName = null, //sql
        $parent = null, // load childs recursively
        $template = '_treeitem.php', // in pro/{tableName}/view/
        $droptarget = null,
        $loader = "ajaxLoad_treeitems", // loader php function to call w ajax >> tree_root.dataset.loader
        $action = null // str name of JS function
    ){


        $treeid = \app::genid(5);


        if($tableName == null) $tableName = \app::$module;
        if($module == null) $module = \app::$module;
        if($droptarget == null) $droptarget = \app::$module;
        //resolve treeitem loader php fun
        // TODO loader default to TreeController if not callable $module::ajaxLoad_treeitems
        //if($loader == null) $loader = "ajaxLoad_treeitems";
        if(is_callable(($module."Controller::".$loader))){
            $loader = $module."/".$loader;
        }elseif (is_callable(($tableName."Controller::".$loader))) {
            $loader = $tableName."/".$loader;
        }else{
            $loader = "\bony\Tree/ajaxLoad_treeitems";
        }
        //if($loader == null) $loader = $module ."/". "ajaxLoad_treeitems";


        

        // init tree DOM
        $return = <<<EOT
        <div id='{$treeid}' class='tree' role='tree' 
            data-module='{$module}'
            data-tablename='{$tableName}' 
            data-ti_selfid=0 
            data-loader='{$loader}'
            data-action='{$action}'
            data-template='{$template}'
            data-droptarget='{$droptarget}'

            data-dragdata='0 {$tableName}'

            data-id=""
            ondragover="dragover(event)"
            ondragleave="dragleave(event)"
            ondrop="treeitem_drop(event)"
            data-is_open=1
            data-level=0
        >
EOT;




        $return .= self::gen_treeitems(
            $module,
            $parent,
            $template,
            $loader
        );



        $return .= <<<EOT
        </div>
EOT;






        $return .= <<<EOT
        <script style='display:none'>
            treegrid_init("#{$treeid}");
            document.querySelector("#{$treeid}").focus();
        </script>

EOT;

        \bony\TreeController::load_js();
        return $return;
    }// end fun generate_view
}//endclass TreeV