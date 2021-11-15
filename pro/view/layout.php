<!DOCTYPE html>

<head>

	<meta id="meta" name="viewport" content="width=device-width initial-scale=1.0" />

	<link href="css/main.css" type="text/css" rel="stylesheet" media="all">
	
	<meta charset="UTF-8">
  <meta name="description" content="<?= $_SESSION['division']['description'] ?>">
  <meta name="keywords" content="<?= $_SESSION['division']['keywords'] ?>">
  <meta name="author" content="István Nagy 314">

	<title><?= $_SESSION['division']['name'].'/'.\app::$module.'/'.\app::$action ?><?= $data['title'] ?></title>


	<?php
		\app::include_CSSFILES();
		\app::include_JSFILES();
	?>

	<script type="text/javascript" >


		function toggleActionMenu(element = null){
			//console.log(element);
			if( (element == null) ||  (element.tagName != 'DIV') ) element = document.getElementById("actionmenu");
			//console.log(element);

			if(document.getElementById("shadow").style.display == "block") {
				document.getElementById("shadow").style.display = "none";

				// may apply this clear func for all events?
				for (var i = 0; i < document.getElementById('shadow').children.length; i++) {
					//console.log(foo.children[i].tagName);
					document.getElementById("overlay_pasteboard")
					.appendChild(document.getElementById('shadow').children[i]);
				}

				document.getElementById("container").style.display = "block";
				//document.getElementById("actionmenu").style.display = "none";
			}else{
				document.getElementById("shadow").appendChild(element);
				element.style.display='block';
				document.getElementById("shadow").style.display = "block";
				document.getElementById("container").style.display = "none";
				//document.getElementById("actionmenu").style.display = "block";
				//document.getElementById("component-menu").style.display = "none";
			}
		}




		function onDocumentKeyUp(e){
			switch(e.key){
				case "Escape":
					if( document.getElementById("shadow").style.display == "block")
							toggleActionMenu(document.getElementById("actionmenu"));
					e.target.blur();
				break;

				case "F2": toggleActionMenu(document.getElementById("actionmenu"));
				break;
			}
		}



		function onDocumentResize(){
			console.log("resize " + document.getElementById("container").scrollHeight);

			if( screen.width > 480){
				document.getElementById("fluidmenu").style.height = 
					Math.max(document.getElementById("container").scrollHeight, window.outerHeight ) +"px";
			}else{
				document.getElementById("fluidmenu").style.height = "auto";
			}

		}//END onDocumentResize..................................................




		// if document ready to play with it:
		window.addEventListener("pageshow", function () {
			//<DEBUG>
			//document.getElementById("container").style.height="2000px";
			//<DEBUG/>

			try { document.getElementById("shadow").style.display = "none";
			} catch (error) { console.log(error); }

			try { document.addEventListener("keyup", onDocumentKeyUp);
			} catch (error) { console.log(error); }

			/* menu buttons listeners */
			try {

				document.querySelector("#fluidmenu #icn-home")
					.addEventListener("click", function() {
						window.location.href="?r=<?= $_SESSION['division']['default_action'] ?>";
						console.log("default_action trigger JS");
					});

				document.querySelector("#fluidmenu #icn-menu").addEventListener("click",toggleActionMenu);

			} catch (error) { console.log(error); }


			/* JS dropdown menu mobile view */
			try {
				// for each menu item, add onClick event handler, change display & class
				document.querySelectorAll("#menu-action-m li").forEach(function(element)
				{
					element.addEventListener("click", function (e)
					{
						e.stopPropagation();
						//console.log(this);
						this.querySelectorAll("ul").forEach(function(obj)
						{
							if(obj.style.display == "block"){
								obj.style.display = "none";
							}else{
								obj.style.display = "block";
							};
						});

					});
				})

			} catch (error) {
				console.log(error)
			}


			/* TODO JS dropdown menu FLUIDMENU */
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
	<div id="shadow" onclick="toggleActionMenu()"> <!-- black overlay for dialogs and mobile menus -->
	</div>


	<div id="container" style="" >


		<nav id="fluidmenu" class=""><!-- SIDEBAR -->

			<xml class="_desktop" >
			<?php include ("css/icn/home_lo.svg"); ?>
			</xml>

			<xml class="" >
			<?php include ("css/icn/menu_lo.svg"); ?>
			</xml>



			<div id="fluidmenu-action-side" class="_desktop"><!-- SIDEBAR dynamic elements -->
				<ul>
					<!--<li>menuitem 1
						<ul>
							<li><a href="" onload="this.addEventListener('click',function(e){e.stopPropagation(); e.preventDefault(); e.stopImmediatePropagation();}">sub 1</a></li>
							<li><a href="">sub menuitem 2</a></li>
						</ul>
					</li>
					<li><a href="">menuitem 2</a></li>
					<li><a href="">menuitem 3</a></li>-->
				</ul>
			</div>

		</nav>











		<!-- to swap menu -->
		<div id="overlay_pasteboard" style="display:none">

			<?php \app::add_overlays(); ?>

			<div id="xhttp_overlay"></div>

			<div id="actionmenu">

				<?php /*
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
				*/
				?>


				<!-- module generated PHP ul-li menu of local actions, in overlay -->
				<nav id="menu-action-m">
					<!-- replace with PHP -->
					<!-- <ul><p>MODULE NAME</p>
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
					<br><br> -->

					<ul>
					<?php if(isset(self::$menu)) echo \bony\Ulmenu::gen_ulmenu(self::$menu); ?>
					</ul>
					
					<hr/>
					
					<ul>
					<?php if(isset($_SESSION['division']['pagemenu'])) 
							echo \bony\Ulmenu::gen_ulmenu($_SESSION['division']['pagemenu']); ?>
					</ul>


					<?php /*
					<hr/>
					<!-- manual menu to ease navigation -->
					<ul>
						<li><a href="javascript:toggleActionMenu()">CANCEL</a></li>
						<li><a href="javascript:;">HOME</a></li>
						<li><a href="javascript:;">NAVIGATION</a></li>
						<li><a href="javascript:;">FAVORITES</a></li>
					</ul>
					*/
					?>

				</nav>

			<!-- ------------end action menu------------ --></div>



		</div><!-- end pasteboard -->





		<header><span class="logo"><?= $_SESSION['division']['name'] ?></span></header>





		<main class="content "  > <!-- style="border: 1px solid #4D4D4D;" -->

			<?= $content ?>

	</main>


</body>