<?php

	include $_SESSION['MODULEPATH'].'/pro/model/test.php';
	

	function test(){
		global $db;
		global $testModel;

		include dirname(dirname(__FILE__))."/model/test_rules.php";


		$result = $db->division();
		//$result=$result->where('name LIKE ?',['%alma%']);
		$rows=$result->fetchAll();




		$table = "<div style='display:flex'>";
		foreach( $rows as $row){
			$buf = var_export($row->getData(),true);
			$table.="<div style='flex:auto;'>{$buf}</div>";
		};
		$table.="</div>";

		$content = "<h1>TEST</h1><h3>" . var_export($rules,true) . "</h3>"
		."<pre>" . $table . "</pre>"; //(string)$GLOBALS['testModel'];
		 

/* 		$model = new testModel($db, 'division');

		$content = "<h1>TEST</h1><h3>" .var_export($model, true). "</h3>"; */

		include $_SESSION['BASEPATH'].'/pro/view/layout.php';	
	}

