<!DOCTYPE html>

<head>
	
	<meta id="meta" name="viewport" content="width=device-width; initial-scale=1.0" />
	
	<link href="css/main.css" type="text/css" rel="stylesheet" media="all">
	

	<style>
		.content p {margin: .2em;}
		
		.actiondiv {
			 flex: 0 0 100%;

			 background-repeat:repeat-x;
			 background-color:#333;
			 text-align:center;
			 text-transform:uppercase;
			 letter-spacing:1px;
			 color:#FFF;
			 font-weight:bold;
			 text-shadow: 2px 2px 1px #333;
		
			 border-bottom: 1px #FFF solid;
			 border-top: 1px #666 solid;
			 border-radius: 6px;
		
			 display:flex;
			 padding:4px;
		}
		
	</style>
	
	<script type="text/javascript" >
		function onScroll_HeaderHMenu() {
			
			switch(document.getElementById("menuwrapper").style.position) {
				case "relative": //console.log("is relative");
					if (window.pageYOffset > document.getElementById("menuwrapper").offsetTop) {
						document.getElementById("menuwrapper").style.position="fixed";
						document.getElementById("HMenuLogo").classList.remove("hidden");
					}
				break;
				case "fixed": console.log("is fixed");
					if (
						window.pageYOffset < document.getElementsByTagName("header")[0].clientHeight
						) {
							//console.log("to relative");
							document.getElementById("menuwrapper").style.position="relative";
							document.getElementById("HMenuLogo").classList.add("hidden");
						}
				break;
				default: console.log("is default");
			}		
				
		}//END onScrooll_HeaderHMenu.............................................
		
		
		
		
		function onDocumentResize(){
			console.log("resize " + document.getElementById("container").scrollHeight);
			document.getElementById("fluidmenu").style.height = Math.max(document.getElementById("container").scrollHeight,
																		window.outerHeight
																		) +"px";
			

		}//END onDocumentResize..................................................

		
		
		
		// if document ready to play with it:
		window.addEventListener("pageshow", function () {
			//<DEBUG>
			document.getElementById("menuwrapper").style.position="relative";
			document.getElementById("container").style.height="2000px";
			//<DEBUG/>			
			
			document.getElementById("shadow").style.visibility = "hidden"; 

			window.addEventListener("scroll", onScroll_HeaderHMenu);
			
			window.addEventListener("resize", onDocumentResize);
			onDocumentResize();
			

		});//END DOM ready, window onload........................................	
		
	</script>
	
	
	<style type="text/css">
		#fluidmenu {
			background: black;
			display: table;
			flex-direction: column;
			align-items: start;
			align-self: stretch;
			justify-content: start;
			color: #F3DD92;
			float: left;
			max-width: 20em;
			width: 2em;
			flex: 0 auto;
		}
		
		#fluidmenu ul{
			display: block;
		}
		
		#fluidmenu ul li{
			display: block;
		}


		.content header {width: 100%;
			align-self: start;
		}
	</style>
</head>

<body id="body">
	<div id="shadow"></div>


	<header><span class="logo">HPO alpha 1</span></header>

	



	<div id="container" style="border: 1px solid white" >
		
		
		
		
	<nav id="fluidmenu" class="">
		
		<div style="flex-direction: row; flex: 0 auto;  flex-wrap: wrap; justify-content: space-around;">
		<button style="flex: 0 0 auto;"> <img src="./css/img/home.svg" alt="H" width="16" heigth="auto" > </button>


		<!-- Created with Inkscape (http://www.inkscape.org/) -->
		<svg class="ico1em3" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" height="42" width="42" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 11.1125 11.1125">
		 <g transform="translate(0 -285.89)">
		  <g class="svg_dark">
		   <rect height="7.4657" width="7.0356" y="289.57" x="2.0384"/>
		   <path d="m5.5563 285.94 1.9368 3.3546 1.9368 3.3546h-3.8736-3.8736l1.9368-3.3546z" transform="matrix(1.4263 0 0 .78719 -2.3688 60.85)"/>
		   <rect height="2.538" width="1.2661" y="287.03" x="7.8079"/>
		  </g>
		  <g id="H" aria-label="H" class="svg_light">
		   <path fill="#fff" d="m4.0734 291.51h0.85333v1.2612h1.2589v-1.2612h0.8535v3.3092h-0.8534v-1.403h-1.2589v1.403h-0.8534z"/>
		  </g>
		 </g>
		</svg>	

		<!-- Created with Inkscape (http://www.inkscape.org/) -->
		<svg class="ico1em3" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" height="42" width="42" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 11.1125 11.1125">
		 <metadata>
		  <rdf:RDF>
		   <cc:Work rdf:about="">
			<dc:format>image/svg+xml</dc:format>
			<dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>
			<dc:title/>
		   </cc:Work>
		  </rdf:RDF>
		 </metadata>
		 <g transform="translate(0 -285.89)">
		  <g class="svg_light">
		   <rect ry=".85151" height="2.0815" width="9.7535" y="286.78" x=".70528"/>
		   <rect ry=".85151" height="2.0815" width="9.7535" y="290.5" x=".70528"/>
		   <rect ry=".85151" height="2.0815" width="9.7535" y="294.21" x=".70528"/>
		  </g>
		 </g>
		</svg>
		</div>

		<!-- <ul>
			<li>menuitem 1
				<ul>
					<li>sub 1</li>	
					<li>sub menuitem 2</li>	
				</ul>
			</li>
			<li>menuitem 2</li>	
			<li>menuitem 3</li>					
		</ul> -->	
	</nav>	
	
		
	

	<nav style="display: flow-root;" class="hidden">
		<div id="menuwrapper"> 
			<div id="mainmenu"> <div id="HMenuLogo" class="floatleft white hidden" style="flex: 0 0 auto;" >HPO alpha 1</div>
				<ul>
					<li>menuitem 1
						<ul>
							<li>menuitem 2</li>	
							<li>menuitem 3</li>	
						</ul>
					</li>
					<li>menuitem 2</li>	
					<li>menuitem 3</li>					
				</ul>
			</div>		
		</div>	
	</nav>
		
		
		
		
		
		
		
		
		<section class="content " style="border: 1px solid #4D4D4D;">

			<header><span class="logo">HPO alpha 1</span></header>

			<p>
				<label>Root Division name:
					<input type="text"  />		
				</label>
			</p>
			
			<p>
				<label>Root Division name:
					<input type="text" placeholder="ldsfjqofqnewrcSS"  />		
				</label>
			</p>	
			
			<p>
				<label>Root Division name:
					<input type="text" placeholder="ldsfjqofqnewrcSS"  />		
				</label>
			</p>	
			
			<p>
				<label>Root Division name:
					<input type="text" placeholder="ldsfjqofqnewrcSS"  />		
				</label>
			</p>
			
			
			<p style="flex: 0 0 100%;" > 
				<input type="button" value="OK" />
				
				<label>vagy ne:
					<input type="button" value="CANCEL"  />		
				</label>
			</p>			
			
			<p class="actiondiv"> okézd le
				<input type="button" value="OK" />
				
				<label>vagy ne:
					<input type="button" value="CANCEL"  />		
				</label>
			</p>
		</section>
	</div>
		

	
</body>