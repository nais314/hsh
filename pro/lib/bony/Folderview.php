<?php namespace bony;
class Folderview {

  /**
   move to TreeController????
  */

  public static function resolve_template(
    $template = '_treeitem.php'
  ){
    // resolve templatefile like an autoloader:
    if($template == null) $template = '_treeitem_noaction.php';
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




  public static function gen_treeitems(
    $path = null,
    $template = '_treeitem_noaction.php',
    $level = 0
  ){
    if(!$path) return false;

    $template = self::resolve_template($template);
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
            $return .= self::gen_treeitems(
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
    return $return;
  }



  public static function add_new_btn(){

  }



  /**
     MAIN ------------------------------------------------
   */
  public static function folderview(
    $path = null, // load childs recursively
    $template = '_treeitem_noaction.php', // in pro/{tableName}/view/
    $droptarget = null,
    $action = null //TODO str name of JS function: binded by treeinit JS func
  ){


    $treeid = \app::genid(5);


    if($path == null or !file_exists($path)){
      return false;
    }


    // init tree DOM
    $return = <<<EOT
    <div id='{$treeid}' class='tree' role='tree'
      data-ti_selfid=0
      data-action='{$action}'

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
      $path,
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