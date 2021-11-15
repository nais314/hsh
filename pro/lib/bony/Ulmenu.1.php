<?php namespace bony;
/**
 * division menus
 * view/module menus
 * breadcrumbs
 */

class Ulmenu {

    public static function gen_ulmenu(
        $menu,
        $headline = null //\app::$module
        //$linktag = true // @REMOVE???
    ){
        $m = "";
        if(is_array($menu))
        foreach($menu as $key => $value){
            $m .= "<ul>";
            if($headline != null) { 
                $m .= "<p>".$headline."</p>";
            }else {
                if(! is_int($key)) $m .= "<p>".$key."</p>";
            }
            //...

            $m .= "<li>";

            if(is_array($value)){
                $k = key($value);
                $v = current($value);

                # SUBMENU --------------------------
                # ["menuitem" => ["view"=>"?r=module/view&id="]]
                #   <ul><p>MODULE NAME</p>
                #       <li>menuitem 1
				#   		<ul>
				#   			<li><a href="">sub 1</a></li>
                if(is_array($v)){
                    $m .= self::gen_ulmenu( $v, $headline = $k);
                }

                # is link ---------------------------
                # ["google" => "www.google.com"]
                # ["go back" => "javascript:window..."]
                else if(\app::is_href($v)){
                    $m .= "<a href='{$v}'>{$k}</a>";
                }


            }//end isarray-----------------------------------------
            
            else if(is_string($value)){
                if(\app::is_href($value)){
                    $k = (is_int($key))? $value : $key;
                    $m .= "<a href='{$value}'>{$k}</a>";
                }
            }

            $m .= "</li>";
            $m .= "</ul>";
        }// end foreach menu array

        
        return $m;
    }
}// end class