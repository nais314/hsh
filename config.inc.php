<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE); // | E_NOTICE

/* header("Cache-Control: private");
header("Pragma: private"); */

define('DS', DIRECTORY_SEPARATOR);

define('DEBUGLEVEL', 0);


/**
app scenarios: common, lockdown, readonly, lanonly, localonly, test etc...
used @ access control ----------------------------------------------------------*/
$_SESSION['scenario'] = 'common';

/**
enable HTTP auth while testing site:......................... */
/*     if (
		!isset($_SERVER['PHP_AUTH_USER'])
		|| !($_SERVER['PHP_AUTH_USER'] == 'test' &&
			 $_SERVER['PHP_AUTH_PW']   == 'test')
	) {
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		echo '<center><h2>Tests are running - please come back later or get test access!</h2></center>';
		exit;
	} */
//....................................................

/**
Enable complete lockdown:  */
/* echo '<center><h2>Maintenance in progress - please come back later!</h2></center>';
exit; */
//....................................................





/**
init Media info -----------------------------------------------------------------*/
//echo $_SERVER['HTTP_USER_AGENT'] . "\n<br/>";
/* if(!isset($_SESSION['user_agent'])) {
	if( stripos($_SERVER['HTTP_USER_AGENT'],'Links' )  !== false ){
		$_SESSION['user_agent'] = 'Links';
	}elseif(  stripos($_SERVER['HTTP_USER_AGENT'], 'Webkit')  !== false ){
		$_SESSION['user_agent'] =  "Webkit!";
	}elseif(  stripos($_SERVER['HTTP_USER_AGENT'], 'Gecko')  !== false ){
		$_SESSION['user_agent'] =  "Gecko!";
	};

	if( stripos($_SERVER['HTTP_USER_AGENT'],'Mobile' )  !== false ){
		$_SESSION['user_agent'] =  "mobile";
	}
	if( stripos($_SERVER['HTTP_USER_AGENT'],'Tablet' )  !== false ){
		$_SESSION['user_agent'] =  "tablet";
	}
}
 */



/**
init app ========================================================================*/

class app {
	public static $db, $pdo = null;
	public static $dbname = 'hsh';
	public static $logger = null;
	public static $ac_check = null;
	#inited in index.php at routing
	public static
		$basepath, $modulepath, $module, $action = null;
	public static
		$controllerClass, $modelClass, $tableName = null;

	public static $arr_JS = [];  // array to hold required JS files
	public static $arr_CSS = []; // array to hold required CSS files
	public static $arr_OVERLAY = []; // array to hold HTML to inject in layout

	/**
	layout
	get layout file name */
	public static function layout($layout=null){

		if( $layout == null ) {
			return self::$basepath.DS.'pro'.DS.'view'.DS.'layout.php';
		}else{
			if (file_exists (self::$basepath.DS.'pro'.DS."view".DS.$layout.".php") ){
				return self::$basepath.DS.'pro'.DS."view".DS.$layout.".php";
			}elseif (file_exists (self::$modulepath.DS."view".DS.$layout.".php") ) {
				return self::$modulepath.DS."view".DS.$layout.".php";
			}
		}

	}
	public static function echo_layout($layout=null){
		echo self::layout($layout);
	}

	/**
	render
	evaluate view file for $content variable */
	public static function render($viewfile, &$data = null){ #i: $data will be available in view!
		ob_start();
			if(file_exists(\app::$modulepath.DS."view".DS.$viewfile.".php")){
				include \app::$modulepath.DS."view".DS.$viewfile.".php";
			}elseif(file_exists(\app::$basepath.DS."pro".DS."view".DS.$viewfile.".php")) {
				include \app::$basepath.DS."pro".DS."view".DS.$viewfile.".php";
			}else{
				\app::$logger->log('error', \app::$modulepath.DS."view".DS.$viewfile.".php");
			}
		return ob_get_clean();
	}



	/**
	 REDIRECT via JS
	 */
	public static function redirect($url){
		\app::$logger->debug(__METHOD__.': '.$url);
		//echo "<script>window.location.href='$url'</script>";
		header("Location: $url");
		die('REDIRECTED');
	}


	/**
	 GET BASE URI
	*/
	public static function baseURI(){
		if(empty($_SERVER['QUERY_STRING'])){
			return $_SERVER['REQUEST_URI'];
		}else{
			return substr(
				$_SERVER['REQUEST_URI'],0,
				strlen($_SERVER['REQUEST_URI']) - strlen($_SERVER['QUERY_STRING']) - 1
			);
		}

	}


	/**
	genid
	*/
	public static function genid($len = 5) {
		$a = range('A','Z');
		return $a[array_rand($a)] . bin2hex(random_bytes($len));
	}



	/**
	INTERFACE for 3rd parties
	 */
	public static function add_JSFile($js=null){
		\app::$arr_JS[] = $js;
		\app::$logger->debug(__METHOD__." - ".var_export(\app::$arr_JS,true));
	}
	/**
	add JS files to layout header
	*/
	public static function include_JSFILES(){
		\app::$logger->debug(var_export(__METHOD__." - ".\app::$arr_JS,true));
		foreach(\app::$arr_JS as $JS) {
			if(substr($JS, -3)=='.js'){
				if(file_exists("js".DS.$JS)){
					echo "<script style='display:none' src='js/{$JS}'></script>";
				}elseif(file_exists($_SESSION['division']['folder'].DS.'js'.DS.$JS)) {
					echo "<script style='display:none' src='"
						.$_SESSION['division']['folder'].DS."js".DS.$JS
						."'></script>";
				}else{
					echo "<script style='display:none' type='text/javascript' src='{$JS}'></script>";
				}
			}else {
				echo "<script style='display:none' type='text/javascript'>{$JS}</script>";
			}
		}
	}

	/**
	INTERFACE for 3rd parties
	 */
	public static function add_CSSFile($css=null){
		\app::$arr_CSS[] = $css;
	}
	/**
	add CSS files to layout header
	*/
	public static function include_CSSFILES(){
		foreach(\app::$arr_CSS as $CSS) {
			if(substr($CSS, -4)=='.css'){
				if(file_exists('css'.DS.$CSS)){
					echo "<link href='css/{$CSS}' type='text/css' rel='stylesheet' media='all'>";
				}elseif(file_exists($_SESSION['division']['folder'].DS.'css'.DS.$CSS)) {
					echo "<link href='"
						.$_SESSION['division']['folder'].DS.'css'.DS.$CSS
						."' type='text/css' rel='stylesheet' media='all'>";
				}
			}else {
				echo "<style>{$CSS}</style>";
			}
		}
	}

	/**
	add content to layouts overlay_pasteboard
	 */
	public static function add_overlays(){
		foreach(\app::$arr_OVERLAY as $div){
			echo $div;
		}
	}

	/**
	check if url?
	maybe @DEPRECATED in fav is_href?
	 */
	/* public static function is_url(&$url){
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$url = filter_var($url, FILTER_SANITIZE_URL);
			return true;
		} else {
			return false;
		}
	} */
	/**
	 check if valid for: <a href=
	 */
	//TODO naming: it is for http links only
	public static function is_href(&$url){
		if (
			$url == ""
		 || (strlen($url) > 10 && substr_compare($url, "javascript:", 0))
		 || (strlen($url) >  2 && substr_compare($url, "?r=", 0))
		 || (strlen($url) >  0 && substr_compare($url, "#", 0))
		){
			return true;
		}

		if( strpos($url, "http", 0) )
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$url = filter_var($url, FILTER_SANITIZE_URL);
			return true;
		}

		return false;
	}


	/**
	 execute cgi-bin
	   system ( string $command [, int &$return_var ] ) : string
	 */
	public static function execute($command,&$return_var):string{
		if(isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] == true){
			ob_start();
			$return = system(CGI_BIN.DS.$_SESSION['division']['folder'].DS.$command,$return_var);
			ob_clean();
			return ($return_var)? $return_var : $return;
			}
	}

}//end class app-----------------

/**
init  \app::$basepath */
if( \app::$basepath == null )
{
	\app::$basepath = __DIR__;
}
//-----------------------------------------------------------------------------





/**
init autoloader ------------------------------------------------------------ */

function autoloader($className){
	//error_reporting(E_ERROR);
	#overhead: os compatibility
	$className = str_replace('\\', DS, $className );
	$class = $className.'.php';
	file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." ".$class." ".$className."\n", FILE_APPEND);


	# load from /pro/lib/+className/class.php
	if(file_exists('pro'.DS.'lib'.DS.$class)){
	  include_once('pro'.DS.'lib'.DS.$class);
		file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." 1 ".'pro'.DS.'lib'.DS.$class."\n", FILE_APPEND);
	}
	# pro/lib/ +class/class.php
	elseif(file_exists('pro'.DS.'lib'.DS.$className.DS.$class)){
		include_once('pro'.DS.'lib'.DS.$className.DS.$class);
		file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." 2 ".'pro'.DS.'lib'.DS.$className.DS.$class."\n", FILE_APPEND);
	}
	/* elseif(file_exists('pro'.DS.$class)){
		include_once('pro'.DS.$class);
		file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." 3\n", FILE_APPEND);
	} */
	elseif(file_exists('pro'.DS.'lib'.DS.$className.DS.basename($className).'.php')){
		include_once('pro'.DS.'lib'.DS.$className.DS.basename($className).'.php');
		file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." 4 ".'pro'.DS.'lib'.DS.$className.DS.basename($className).'.php'."\n", FILE_APPEND);
	}else{
		if(isset(\app::$modulepath)){
			# load *Model or *Controller
			if(file_exists(\app::$modulepath.DS.$class)) {
					include_once(\app::$modulepath.DS.$class);
					file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." A ".\app::$modulepath.DS.$class."\n", FILE_APPEND);
			}
			# load module/lib/
			elseif (file_exists(\app::$modulepath.DS.'lib'.DS.$class)) {
					include_once(\app::$modulepath.DS.'lib'.DS.$class);
					file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." B ".\app::$modulepath.DS.'lib'.DS.$class."\n", FILE_APPEND);
			}
			# load module/lib/classname/
			elseif (file_exists(\app::$modulepath.DS.'lib'.DS.$className.DS.$class)) {
				include_once(\app::$modulepath.DS.'lib'.DS.$className.DS.$class);
				file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." C ".\app::$modulepath.DS.'lib'.DS.$className.DS.$class."\n", FILE_APPEND);
			}

			# module base/./
			/* elseif(file_exists(\app::$modulepath.DS.\app::$module.$class)){
			  include_once(\app::$modulepath.DS.\app::$module.$class);
				file_put_contents('pro/log/autoloader.log',date("Y.m.d G:i:s")." B ".\app::$modulepath.DS.\app::$module.$class."\n", FILE_APPEND);
			}  */

		}
	}
}// end_autoloader_________________
spl_autoload_register('autoloader');



/**
init logger ---------------------------------------------------------------*/
//use Psr\Log;
//use Psr\Log\LogLevel;

app::$logger = new \Psr\Log\Logger(); //TODO add filename



if(DEBUGLEVEL >= 5){
	\app::$logger->debug(var_export($_SERVER, true));
}







/**
init Database -----------------------------------------------------------------*/
/** ENABLE FOR MYSQL BACKEND -------------------- */
/* $dsn = 'mysql:host=localhost;dbname='.app::$dbname;
$username = 'pmauser';
$password = 'obrokodobro';
$options = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
try {
	app::$pdo = new PDO($dsn, $username, $password, $options );
	//$db = new LessQL\Database( $pdo );
}catch(Exception $e){
	//echo "Failed: " . $e->getMessage();
	\app::$logger->log(Psr\Log\LogLevel::ERROR,$e->getMessage());
}
app::$db = new LessQL\Database( app::$pdo );

unset($dsn, $username, $password, $options);
 */
/** END ENABLE FOR MYSQL BACKEND -------------------- */


/** ENABLE FOR SQLITE BACKEND -------------------- */
$dsn = 'sqlite:'.\app::$basepath.'/pro/_data/hsh.db3';
try {
	app::$pdo = new PDO($dsn, $username = '', $password = '', $options = null );
}catch(Exception $e){
	echo "ERROR: " . $e->getMessage() . "<br/>\n" . \app::$basepath."/pro/_data/hsh.db3<br/>\n";
	\app::$logger->log(Psr\Log\LogLevel::ERROR,$e->getMessage());
	die("database error");
}
if(empty(app::$pdo)){
	die("EMPTY PDO");
}
/** END ENABLE FOR SQLITE BACKEND -------------------- */

/**
@NEW to implement:
SQL table PRE and POST-fixes
 */
define("TBPREFIX", ""); # table prefix: no2_post // useful if 1 can have only 1 db, to separate installs
define("TBPOSTFIX", ""); # 1 can use rootdiv as postfix to partition database if needed



//app::$logger->log('DEBUG', $_SESSION['division']['id']);
/**
init Division with pdo/resql - if needed (for Level-III apps) --------------------*/
//app::$logger->log('DEBUG session', var_export($_SESSION['division'],true));
//if(true)
//if(false)
if(!isset($_SESSION['division']))
{
	//\app::$logger->debug('[parse url] '.var_export($_SERVER,true));

	$data = \bony\resql::fetch('hostname LIKE ?', '%'.$_SERVER['HTTP_HOST'].'%', "division");
			/* $stmt = \app::$pdo->prepare("SELECT * FROM division WHERE hostname LIKE ?");
			echo "stmt: ",var_export($stmt),"<br>\n";
			if(empty($stmt)){
					die('STMT ERROR 2');
			}
			echo $stmt->execute(['%314%'])."<br/>";
			echo var_export($stmt->errorInfo())."<br/>";
			$data = $stmt->fetch(\PDO::FETCH_ASSOC);
			echo "err: ", var_export($stmt->errorInfo())."<br/>";
			echo "data: ",var_export($data)."<br/>"; */
	#$data = \bony\resql::fetch('hostname = ?', 'www2.314.hu', "division");
	if($data) {
		$_SESSION['division'] = $data;
		#? override scenario if needed: LOCKDOWN?
		if( isset($_SESSION['division']['scenario'])
		&& !empty($_SESSION['division']['scenario'])) $_SESSION['scenario'] = $_SESSION['division']['scenario'];

		if(empty($_SESSION['division']['rootdiv'])) $_SESSION['division']['rootdiv'] = $_SESSION['division']['id'];

	}else{
		die("no division data error");
	}
	if(DEBUGLEVEL >= 2) \app::$logger->debug('DEBUG' . var_export($_SESSION['division'],true));

	# load $pagemenu // $_SESSION['division']['menu']
	# and other customisations

	if(file_exists('folders'.DS.$_SESSION['division']['folder'].DS.'pro'.DS.'config.inc.php')){
		# add menu, etc in this file:
		$_SESSION['division']['pagemenu'] = []; //cleanup
		include('folders'.DS.$_SESSION['division']['folder'].DS.'pro'.DS.'config.inc.php');
	}
}

/**
@NEW to implement:
constant for SQL querys to stick to this ROOT-DIVISION */
define("WHERE_ROOTDIV",(" AND rootdiv = ".$_SESSION['division']['rootdiv']));
if(DEBUGLEVEL >= 2) \app::$logger->debug("WHERE_ROOTDIV ".WHERE_ROOTDIV);


/**
@NEW to implement:
path to CGI-BIN */
#define("CGI_BIN","/srv/cgi-bin");
#define("CGI_BIN",'folders'.DS.$_SESSION['division']['folder'].DS.'cgi-bin');
define("CGI_BIN","/var/www/vhosts/www2.314.hu/cgi-bin");