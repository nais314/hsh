<?php namespace bony;
class Treeview {

  /**
   move to TreeController????
  */

  public static function resolve_template(
    $template = '_treeitem.php'
  ){
    // resolve templatefile like an autoloader:
    if($template == null) $template = '_treeitem.php';
    if(stripos($template, ".php") == -1) $template = $template.'php';
    if(file_exists($template)){
    }elseif(file_exists('pro/'.$module.DS.'view'.DS.$template)){
      $template = 'pro/'.$module.DS.'view'.DS.$template;
    }elseif (file_exists(\app::$modulepath.DS.'view'.DS.$template)) {
      $template = \app::$modulepath.DS.'view'.DS.$template;
    }elseif (file_exists('pro/'.$dir.DS.'view'.DS.$template)) {
      $template = 'pro/'.$dir.DS.'view'.DS.$template;
    }

  return $template;
  }//......................................................




  public static function gen_treeitems_path(
    $path = null,
    $template = '_treeitem.php',
    $level = 0
  ){
    $template = resolve_template($template);
    $return = "";

    $c = 0;
    if ($handle = opendir($path)){
      $data = [];
      while (false !== ($filename = readdir($handle)))
      {
        if(is_dir($path.DS.$filename)
        and $filename != '.' and $filename != '..')
        {
          $data['name'] = $filename;
          $data['level'] = $level;
          switch($data['level']){
            case 0: $data['css_class'] = 'i1'; break;
            case 1: $data['css_class'] = 'i2'; break;
            case 2: $data['css_class'] = 'i3'; break;
          }
          if(count(scandir($path)) == 2){
            $data['has_child'] = 0;
          }else{
            $data['has_child'] = 1;
          }

          \ob_start();
          include($template); # template uses $data
          $return .= \ob_get_clean();

          // CHILDS
          if ($data['has_child']){
            $return .= self::gen_treeitems_path(
                    $path.DS.$filename,
                    $template,
                    ($level + 1)
                  );
            //\app::$logger->debug($return);
          }

        }//end isdir
      }
    closedir($handle);
    }
  }


  public static function gen_treeitems(
    $module = null, //\app::$module,
    $parent = null, // PARENT sql ID or null - load from this down
    $template = '_treeitem.php',
    //$loader = null // loader php function >> tree_root.dataset.loader
    $level = 0
  ){

    // resolve templatefile like an autoloader:
    if($template == null) $template = '_treeitem.php';
    if(stripos($template, ".php") == -1) $template = $template.'php';
    if(file_exists($template)){
    }elseif(file_exists('pro/'.$module.DS.'view'.DS.$template)){
      $template = 'pro/'.$module.DS.'view'.DS.$template;
    }elseif (file_exists(\app::$modulepath.DS.'view'.DS.$template)) {
      $template = \app::$modulepath.DS.'view'.DS.$template;
    }elseif (file_exists('pro/'.$dir.DS.'view'.DS.$template)) {
      $template = 'pro/'.$dir.DS.'view'.DS.$template;
    }


    $rows = \bony\resql::fetchAll(['parent'=>$parent], null,
                    \app::$tableName,
                    $columns='*',
                    $reassoc=false);





    // foreach rows as data inject templatefile.....................
    //echo $level, " ... ";
    //! $level = (isset($_REQUEST['tree']['level']))? $_REQUEST['tree']['level']+1 : 1;
    //$css_class =  ($level > 1)? (($level % 2 == 0)? 'iodd':'ieven') : "";
    //$css_class =  ($level > 0)? (($level > 1)? 'iodd':'ieven') : "";

    foreach($rows as $data){
      $data['level'] = $level;
      //$data['css_class'] = $css_class;
      /* $data['css_class'] = ($data['has_child'])?
        (($level > 0)? (($level % 2 == 0)? 'iodd':'ieven'): null)
        : (($level > 0)? 'ithird': null); */
      switch($data['level']){
        case 0: $data['css_class'] = 'i1'; break;
        case 1: $data['css_class'] = 'i2'; break;
        case 2: $data['css_class'] = 'i3'; break;
      }
      if(!$data['has_child']){

      }
         

      \ob_start();
        include($template); # template uses $data
      $return .= \ob_get_clean();

      // CHILDS
      if ($data['has_child']){
        $return .= self::gen_treeitems(
                $module,
                $data['id'], //$parent
                $template,
                ($level + 1) //$level
              );
        //\app::$logger->debug($return);
      }
    }

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
    //$tableName = null, //sql table name
    $parent = null, // load childs recursively
    $template = '_treeitem.php', // in pro/{tableName}/view/
    $droptarget = null,
    //$loader = "ajaxLoad_treeitems", //?TODO DEPR loader php function to call w ajax >> tree_root.dataset.loader
    $action = null //TODO str name of JS function: binded by treeinit JS func
  ){


    $treeid = \app::genid(5);


    if($module == null) $module = \app::$module;
    //if($tableName == null) $tableName = \app::$module;
    $tableName = \app::$tableName;
    if($droptarget == null) $droptarget = \app::$module;


    // init tree DOM
    $return = <<<EOT
    <div id='{$treeid}' class='tree' role='tree'
      data-module='{$module}'
      data-ti_selfid=0
      data-action='{$action}'
        data-template='{$template}'

      data-droptarget='{$droptarget}'
      data-dragdata='0 {$tableName}'

      data-id=""
      ondragover="dragover(event)"
      ondragleave="dragleave(event)"
      ondrop="treeitem_drop(event)"
      data-level=0
    >
EOT;




    $return .= self::gen_treeitems(
      $module, //$module,
      $parent,
      $template,
      $level = 0
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