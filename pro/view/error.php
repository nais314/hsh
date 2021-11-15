<style>
body { margin: 0px 0px; padding: 0px 0px; background-color:#990000; }

html, body, #container, #content, center { height: 100% !important; }

div { flex: 0 1 85%; }

#errorback {
	vertical-align:middle;
	text-align:center;
	border: 16px double #000;
	width:90%;
	height:75%;
	padding: 0.3em;
	margin: auto;
	font-weight:bold;
	color:#000;
	font-family:Arial, Helvetica, sans-serif;
	font-size:4em;
    text-decoration:blink;

	display: flex;
	justify-content: center;
	align-items: center;

}
#container {
    display: flex;
    justify-content: space-around;
    align-content: space-around;
    flex-direction: row;
}
</style>

<div id="container">
    <div id='errorback' style="color:#FFF;">
		<div>ERROR:<br><?= $error ?></div>
		<?= $errormessage ?>
    </div>
</div>

<script>
		window.setInterval(function(e){ 
			if( document.getElementById('errorback').style.color != 'rgb(0, 0, 0)') {
				document.getElementById('errorback').style.color= 'rgb(0, 0, 0)';
                document.getElementById('errorback').style.boxShadow=null;
			} else {
                document.getElementById('errorback').style.color= 'rgb(255, 255, 255)';
                document.getElementById('errorback').style.boxShadow='0 0 .5em 10px white';
            };
		}, 700 );
</script>

