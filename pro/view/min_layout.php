<!DOCTYPE html>

<head>
	
	<meta id="meta" name="viewport" content="width=device-width initial-scale=1.0" />
	
	<link href="css/main.css" type="text/css" rel="stylesheet" media="all">

	
	<script type="text/javascript" >
		// if document ready to play with it:
		window.addEventListener("pageshow", function () {
			//<DEBUG>
			document.getElementById("container").style.height="2000px";
			//<DEBUG/>			

		});//END DOM ready, window onload........................................	
	</script>
	

</head>





<body id="body">

	<div id="container" style="" >
		

		<header><span class="logo"><?= $_SESSION['division']['name'] ?></span></header>
		

		<main class="content "  > <!-- style="border: 1px solid #4D4D4D;" -->

			<!-- <header><span class="logo">HPO alpha 1</span></header> -->
			
			<?= $content ?>

		</main>
	
		

</body>