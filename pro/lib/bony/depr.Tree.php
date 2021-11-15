<?php namespace bony;

class Tree {
/**
		  id="1"
          draggable="false"
		  data-droptarget="modelname"
          *data-pk="1"
          data-dragdata="1 category"
          data-custid=""
          data-model="category"

		  *data-parent="0"
          data-has_child="1/0"
          data-childs_loaded="1/0"
		  data-rootdiv="31"
		  *data-name="Customer"

		  *data-is_new="0"
*/
/**
 * tree = tree
    A type of list that may contain sub-level nested groups that can be collapsed and expanded.
*  treegrid = tg
    A grid whose rows can be expanded and collapsed in the same manner as for a tree.
*  treeitem = ti
    An option item of a tree. This is an element within a tree that may be expanded or collapsed if it contains a sub-level group of treeitem elements.

 * https://www.w3.org/TR/2014/REC-wai-aria-20140320/roles#role_definitions
 */



    public function load_js(){
        \app::$arr_JS['treegrid'] = "treegrid.js";
        \app::$arr_JS['dragdrop'] = "dragdrop.js";
    }


        /**
        for trees --------------------------------------------------------
        */
        protected function tree_recalc_levels(&$tree, $level = 0){

            foreach ($tree as &$tn){
                $tn['level'] = $level;
                if(isset($tn['childs'])) self::tree_recalc_levels($tn['childs'], ($level+1) );
            }

        }//....................................




    /**
    for trees --------------------------------------------------------
    */

    public function load_route2selected(
        $tableName = '',
        $selected_id = null
    ){
        
        #load selected + siblings = parents childs
        #load parent until parent in roots

        #if no args:
            if($selected_id == null || $selected_id == "") {
                //\app::$logger->log('info', __METHOD__." > ".$tableName . ' - ' .$selected_id);
                $a = self::ti_load_childs($tableName); // loads root ti-s by default

                return $a;
            }
            //...............................

        $result = \app::$db->$tableName()
        ->select('parent')
        ->where('id', $selected_id);
        $cursor_row = $result->fetch();

            // if(empty($cursor_row)) return false; //err
        //...............................

        #siblings,, same parent (*i: selected is in the set)
        $result = \app::$db->$tableName()
        ->where('parent', $cursor_row['parent'])
        ;
        $rows = $result->fetchAll();
        //if(empty($rows)) return false; // ???

        $a=[]; // init var

        #distill result set <<F&R
        foreach($rows as $row) {
            $a[$row->id] = [
                'name' => $row->name,
                'css_class' => $row->css_class,
                'css_style' => $row->css_style,
                'level' => $level,
                'has_child' => $row->has_child,
            ];
            if($row->id == $selected_id) $a[$row->id]['css_class'] .= " iselected";
        }

        //\app::$logger->debug(__METHOD__." ".var_export($a,true));

        //...............................

        do {
            #load parents parent
            #load its childs
            #assemble a rray (create chain)

            $buffer = $cursor_row->parent;

            $result = \app::$db->$tableName()
            ->select('parent')
            ->where('id', $cursor_row->parent);
            $cursor_row = $result->fetch();

            #siblings:
            $result = \app::$db->$tableName()
            ->where('parent', $cursor_row['parent']);
            $rows = $result->fetchAll();

            $b=[];

            foreach($rows as $row) {
                #distill result set <<F&R
                $b[$row->id] = [
                    'name' => $row->name,
                    'css_class' => $row->css_class,
                    'css_style' => $row->css_style,
                    'level' => $level,
                    'has_child' => $row->has_child,
                ];

                if($row->id == $buffer) {
                    $b[$row->id]['childs'] = $a;
                    $b[$row->id]['childs_loaded'] = true; // for HTML output & JS
                }

            }

            #swap (create chain)
            $a = $b; //unset($b);

        }while($cursor_row['parent'] != null);


        self::tree_recalc_levels($a, $level = 1);

        //\app::$logger->log('info', var_export($a,true));

    return $a;

    }








    /**
    for trees --------------------------------------------------------*/
    public function ti_load_childs(
        $tableName = '',
        $parent=null,
        $level = 0,
        $recursive = false){

        $rows = \bony\resql::fetchAll($wherestr=['parent'=>$parent], $var_arr=null, $tableName); //($parent == null)? "parent IS NULL":$parent
        //$rows = \bony\resql::reassoc($rows);
        //\app::$logger->debug(__METHOD__.var_export($rows, true));

        
        foreach($rows as &$row) {
            $row['level'] = $level;
            if($recursive)
            if($row['has_child'] > 0) $row['childs'] =
                self::ti_load_childs($tableName, $row['id'], $level +1, true);
        }

        return $rows;
    }




}