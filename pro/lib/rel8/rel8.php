<?php namespace rel8;
class rel8 {

    /**
     * get_treeitems - return treeitems (recursive)
     * rel8_treeview - init tree
     * 
     */

    public function get_treeitems($parent){
        
    }

    public function rel8_treeview(
        $parent = null
        //$template = '_treeitem.php'
    ){
        $uid = \app::genid(5);

        // init DOM
        $return = <<<EOT
        <div id='{$uid}' class='rel8_tree' role='tree' 

        >
EOT;

        $return .= get_treeitems($parent);
    }// end rel8_treeview



}// end class