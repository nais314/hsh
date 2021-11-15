<?php namespace bony;
echo "<pre>";

chdir('..');

include 'config.inc.php';
include 'pro/lib/bony/Tree.php';

/* echo "Tree::load_tn_childs";

    $tv = Tree::load_tn_childs(
        $tableName = 'division', 
        $parent=null, 
        $prefix='', 
        $level = 0);

    print_r($tv); */

echo "<hr>";

    $tv = Tree::load_route2selected(
        $tableName = 'division', 
        $selected_id = 1);

    print_r($tv);

/* echo "<hr>";    
    $tv = Tree::load_route2selected(
        $tableName = 'division' );

    print_r($tv); */
    

echo "</pre>";