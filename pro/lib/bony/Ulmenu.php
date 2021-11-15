<?php namespace bony;
/**
 * division menus
 * view/module menus
 * breadcrumbs
 */

class Ulmenu {

    public static function gen_ulmenu(
        $menu
    ){
        $m = "";

        foreach($menu as $key => $value){
            if(is_string($value)){
                $m .= "<li>";
                $k = (is_int($key))? $value : $key;
                $m .= "<a href='{$value}'>{$k}</a>";
                $m .= "</li>";
            }

            if(is_array($value)){
                $m .= "<li>";
                $m .= "<p>".$key."</p>";
                $m .= "<ul>";
                $m .= self::gen_ulmenu($value);
                $m .= "</ul>";
                $m .= "</li>";
            }
        }// end foreach menu array

        
        //return "<ul>"."<p>".$key."</p>".$m."</ul>";
        return $m;
    }
}// end class