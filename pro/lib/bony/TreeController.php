<?php
namespace bony;

/**
 * This module can be reached as ?r=\bony\Tree/ajaxLoad_TreeInput_TreeitemChilds
 */

Class TreeController{

        /**
        for trees --------------------------------------------------------
        */
        # common function for all tree inputs to work!!!
        /* public static function ajaxLoad_TreeInput_TreeitemChilds(){
            $tree= \bony\TreeController::ti_load_childs(
                $tableName = $_REQUEST['tree']['tableName'],
                $parent=$_REQUEST['tree']['parent'],
                $level = ++$_REQUEST['tree']['level'],
                $recursive = false);

            echo \bony\Former::gen_tree_nodes($tree);

        }
 */


        /* public function ajaxLoad_treeitems(){
            echo \bony\Treeview::gen_treeitems(
                $tableName = $_REQUEST['tree']['tableName'],
                $parent = ($_REQUEST['tree']['parent'] == 0)? null:$_REQUEST['tree']['parent'],
                $template = ($_REQUEST['tree']['template']=='undefined')? null : $_REQUEST['tree']['template']
                //$loader = 'division/ajaxLoad_treeitems'
            );
        }//----------------------------------------------------------------------
 */

        /**
         * Generic drop handler for same model types, parent-child relationship
         */
        public function drop(){
            //\app::$logger->debug(__METHOD__." ".var_export($_REQUEST,true));
            # pre pre state :
            //$prevData = \bony\resql::load($_REQUEST['drop']['id'], $_REQUEST['drop']['tableName']);
            $prevData = \bony\resql::fetch(["id"=>$_REQUEST['drop']['id']], null, $_REQUEST['drop']['model'], $columns='parent');
            $prevData['parent'] = (int)$prevData['parent'];
            //var_dump($prevData);
            # child data to update:
            $data['parent'] = (int)$_REQUEST['drop']['parent'];
            $data['id'] = (int)$_REQUEST['drop']['id'];
            autoloader($_REQUEST['drop']['model']."\\".$_REQUEST['drop']['model']."Model");
            \bony\resql::save(  $data, 
                                $tableName=$_REQUEST['drop']['model'], 
                                $modelName=$_REQUEST['drop']['model'], 
                                $validate=true);
            # handle prev parent:
            if($prevData['parent']){ # not NULL
                $has_child = \bony\resql::fetch(["parent"=>$prevData['parent']], null, $_REQUEST['drop']['model'], $columns='id');
                if(!$has_child){
                    //var_dump($has_child);
                    $data2 = ['id'=>$prevData['parent'], "has_child"=>0];
                    \bony\resql::save($data2, $tableName=$_REQUEST['drop']['model'], $modelName=$_REQUEST['drop']['model'], $validate=true);
                }
            }
            # update current parent
            $data = ['id'=>$data['parent'], "has_child"=>1];
            //var_dump($data);
            \bony\resql::save($data, $tableName=$_REQUEST['drop']['model'], $modelName=$_REQUEST['drop']['model'], $validate=true);

            echo "OK";
            //return true;
        }//----------------------------------------------------------------------




#################################################################################
# Former Tree class functions:
#################################################################################




        public function load_js(){
            \app::$arr_JS['treegrid'] = "treegrid.js";
            \app::$arr_JS['dragdrop'] = "dragdrop.js";
        }
    
    
            /**
            for trees --------------------------------------------------------
            */
            /* protected function tree_recalc_levels(&$tree, $level = 1){
    
                foreach ($tree as &$tn){
                    $tn['level'] = $level;
                    if(isset($tn['childs'])) self::tree_recalc_levels($tn['childs'], ($level+1) );
                }
    
            }//.................................... */
    
    
    
  
        /**
        for trees --------------------------------------------------------
        */
    
        //!DEPRECATED public function load_route2selected(

    
    
    
    
    
        /**
        for trees --------------------------------------------------------*/
        public function ti_load_childs(
            $tableName = '',
            $parent=null,
            $level = 0,
            $recursive = false){
    
            $rows = \bony\resql::fetchAll(
                $wherestr=['parent'=>$parent],
                $var_arr=null,
                $tableName,
                $columns='*', $reassoc=false); //($parent == null)? "parent IS NULL":$parent
            //$rows = \bony\resql::reassoc($rows);
            if($parent == null) \app::$logger->debug(__METHOD__.var_export($rows, true));
    
            
            foreach($rows as &$row) {
                $row['level'] = $level;

                if($recursive)
                    if($row['has_child'] > 0) $row['childs'] =
                        self::ti_load_childs($tableName, $row['id'], $level + 1, true);
            }
    
            return $rows;
        }
    
//end class    
}