<?php include("config.inc.php"); ?>
<?php
	function pw_bcrypt($v){
		if (version_compare(phpversion(), '5.5.0', '>=')) {
			return password_hash($v, PASSWORD_BCRYPT);
		} else {
			die('only md5 available');
			return md5($v);
		}			
	}

	if (!empty($_POST)){
		
/* 		// Connect to database
		$dsn = 'mysql:host=localhost;dbname=hsh';
		$username = 'root';
		$password = 'obrokodobro';
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		); 
		$pdo = new PDO($dsn, $username, $password, $options );
		$db = new LessQL\Database( $pdo ); */

		$row = $db->division()->createRow( $properties = $_POST['division'] );
		$row->save();
		print_r($row);

		$rootdiv = $row['id'];

		die();



		$row = $db->user()->createRow( $properties = $_POST['user'] );
		$db->setAlias( 'rootdiv', 'division' );

		if($row['password'] != $row['password2']) die('passwords not match');

		unset(  $row['password2'] );

/* 		$result = $db->division()->where('name', '314');
		$division = $result->select('id')->fetch(); */


		$row['division_id'] = $rootdiv;
		$row['rootdiv'] = $rootdiv;
		
		$row['password'] = password_hash($row['password'], PASSWORD_BCRYPT);
		
		$row->save();		

		print_r($row);

		die();
	};

	
?>
<!DOCTYPE html>

<head>
	
	<meta id="meta" name="viewport" content="width=device-width initial-scale=1.0" />
	
	<link href="css/main.css" type="text/css" rel="stylesheet" media="all">
	

	<style>


		
	</style>
	
	<script type="text/javascript" >


		function toggleActionMenu(){
			if(document.getElementById("shadow").style.display == "block") {
				document.getElementById("shadow").style.display = "none";
				document.getElementById("container").style.display = "block";
			}else{
				document.getElementById("shadow").style.display = "block";
				document.getElementById("container").style.display = "none";
			}
		}
		

		function escapeFun(e) {
			//console.log(e);
			if(e.key == "Escape"
				&& document.getElementById("shadow").style.display == "block"
			) toggleActionMenu();
		}


		function onDocumentKeyUp(e){
			switch(e.key){
				case "Escape":
					if( document.getElementById("shadow").style.display == "block") toggleActionMenu();
				break;

				case "F2": toggleActionMenu();
				break;
			}
		}

		
		function onDocumentResize(){
			console.log("resize " + document.getElementById("container").scrollHeight);

			if( screen.width > 480){
				document.getElementById("fluidmenu").style.height = Math.max(document.getElementById("container").scrollHeight, window.outerHeight ) +"px";
			}else{
				document.getElementById("fluidmenu").style.height = "auto";
			}

		}//END onDocumentResize..................................................

		
		
		
		// if document ready to play with it:
		window.addEventListener("pageshow", function () {
			//<DEBUG>
			document.getElementById("container").style.height="2000px";
			//<DEBUG/>			
			
			try { document.getElementById("shadow").style.display = "none";
				} catch (error) { console.log(error); }
			
			try { document.addEventListener("keyup", onDocumentKeyUp);
				} catch (error) { console.log(error); }			

			/* menu buttons listeners */
			try {
				document.querySelector("#fluidmenu #icn-menu").addEventListener("click",toggleActionMenu);
				document.querySelector("#menu-app-icn-m #icn-menu").addEventListener("click",toggleActionMenu);
				} catch (error) { console.log(error); }
			
			
			/* JS dropdown menu mobile view */
			try {
				// for each menu item, add onClick event handler, change display & class
				document.querySelectorAll("#menu-action-m li").forEach(function(element)  
				{
					element.addEventListener("click", function (e) 
					{
						//console.log(this);
						this.querySelectorAll("ul").forEach(function(obj) 
						{
							if(obj.style.display == "block"){
								obj.style.display = "none";
							}else{
								obj.style.display = "block";
							};
						});

						if(this.classList.contains("menu-action-m-ul-li-hover"))
						{
								this.classList.remove("menu-action-m-ul-li-hover");
						} else {
							this.classList.add("menu-action-m-ul-li-hover");
						}
					});
				})
				
			} catch (error) {
				console.log(error)
			}


			/* JS dropdown menu FLUIDMENU */
			try {
				// for each menu item, add onClick event handler, change display & class
				document.querySelectorAll("#fluidmenu-action-side li").forEach(function(element)  
				{
					element.addEventListener("click", function (e) 
					{
						//console.log(this);
						this.querySelectorAll("ul").forEach(function(obj) 
						{
							if(obj.style.display == "block"){
								obj.style.display = "none";
							}else{
								obj.style.display = "block";
							};
						});

						/* if(this.classList.contains("menu-action-m-ul-li-hover"))
						{
								this.classList.remove("menu-action-m-ul-li-hover");
						} else {
							this.classList.add("menu-action-m-ul-li-hover");
						} */
					});
				})
				
			} catch (error) {
				console.log(error)
			}			

		
			window.addEventListener("resize", onDocumentResize);
			onDocumentResize();
			

		});//END DOM ready, window onload........................................	
		
	</script>
	
	
	<style type="text/css">

	</style>
</head>

















<body id="body">
	<div id="shadow"> <!-- black overlay for dialogs and mobile menus -->

		<nav id="menu-app-icn-m"> <!-- mobile app common icon menubar -->

			<xml class="" >
			<?php include ("css/icn/nav_lo.svg"); ?>
			</xml>

			<xml class="" >
			<?php include ("css/icn/home_lo.svg"); ?>
			</xml>
		
			<xml class="" >
			<?php include ("css/icn/fav_lo.svg"); ?>
			</xml>

			<xml class="" >
			<?php include ("css/icn/menu_lo.svg"); ?>
			</xml>
			
		</nav>
		
		<!-- module generated PHP ul-li menu of local actions, in overlay -->
		<nav id="menu-action-m">
			<!-- replace with PHP -->
			<ul><p>MODULE NAME</p>
				<li>menuitem 1
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>	
					</ul>
				</li>
				<li><a href="">menuitem 2</a></li>	
				<li><a href="">menuitem 3</a></li>
			</ul>

			<ul><p>MODULE NAME</p>
				<li>menuitem 1
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
						<li><a href="">menuitem 2</a></li>	
						<li><a href="">menuitem 3</a></li>
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
					</ul>
				</li>
				<li>menuitem 2
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
						<li><a href="">menuitem 2</a></li>	
						<li><a href="">menuitem 3</a></li>
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
					</ul>
				</li>	
				<li>menuitem 3
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
						<li><a href="">menuitem 2</a></li>	
						<li><a href="">menuitem 3</a></li>
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
					</ul>
				</li>
				<li>menuitem 1
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
						<li><a href="">menuitem 2</a></li>	
						<li><a href="">menuitem 3</a></li>
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
					</ul>
				</li>
				<li>menuitem 2
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
						<li><a href="">menuitem 2</a></li>	
						<li><a href="">menuitem 3</a></li>
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
					</ul>
				</li>	
				<li>menuitem 3
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
						<li><a href="">menuitem 2</a></li>	
						<li><a href="">menuitem 3</a></li>
						<li><a href="">sub menuitem 2</a></li>
						<li><a href="">menuitem qweqwe 2</a></li>	
						<li><a href="">menuitemqweqwe 3</a></li>
					</ul>
				</li>				
			</ul>
			
			<ul><p>MODULE NAME</p>
				<li>menuitem 1
					<ul>
						<li><a href="">sub 1</a></li>	
						<li><a href="">sub menuitem 2</a></li>	
					</ul>
				</li>
				<li><a href="">menuitem 2</a></li>	
				<li><a href="">menuitem 3</a></li>
			</ul>			

			<p><hr/></p>

			<!-- manual menu to ease navigation -->
			<ul>
				<li><a href="javascript:toggleActionMenu()">CANCEL</a></li>
				<li><a href="javascript:;">HOME</a></li>
				<li><a href="javascript:;">NAVIGATION</a></li>
				<li><a href="javascript:;">FAVORITES</a></li>
			</ul>			

		</nav>


	</div>
















	
	

	<div id="container" style="border: 1px solid white" >
		
		
		
			
		<nav id="fluidmenu" class="">

			<xml class="_desktop" >
			<?php include ("css/icn/home_lo.svg"); ?>
			</xml>

			<xml class="" >
			<?php include ("css/icn/menu_lo.svg"); ?>
			</xml>		



			<div id="fluidmenu-action-side" class="_desktop">	
				<ul>
					<li>menuitem 1
						<ul>
							<li><a href="javascript:;" onload="this.addEventListener("click",function(e){e.stopPropagation(); e.preventDefault(); e.stopImmediatePropagation();}">sub 1</a></li>	
							<li><a href="">sub menuitem 2</a></li>	
						</ul>
					</li>
					<li><a href="">menuitem 2</a></li>	
					<li><a href="">menuitem 3</a></li>				
				</ul>
			</div>

		</nav>	
	
			
	


		
		<header><span class="logo">HPO alpha 1</span></header>
		
		
		
		
		
		
		<section class="content "  > <!-- style="border: 1px solid #4D4D4D;" -->

			<form action="" method="POST" enctype="application/x-www-form-urlencoded">

				<!-- <header><span class="logo">HPO alpha 1</span></header> -->

				<div>
					<label class="req">Root Division name:&nbsp;</label>
						<input type="text" name="division[name]" required placeholder="required"/>		
					
				</div>
				
				<div  class="req">
					<label>Admin login-name:&nbsp;</label>
						<input type="text" name="user[name]" required placeholder="required"  />		
					
				</div>	
				
				<div class="req">
					<label>Admin password:&nbsp;</label>
						<input type="password" name="user[password]" required placeholder="required"  />		
					
				</div>	
					
				<div class="req">
					<label>retype Admin password:&nbsp;</label>
						<input type="password" name="user[password2]" required placeholder="required"  />		
					
				</div>				
				
				<div class="actiondiv"> 
					<div>
					<label> Submit&nbsp; </label>
					<input type="submit" value="OK" />
					</div>

				</div>

			</form>
		</section>
	
		

	
</body>