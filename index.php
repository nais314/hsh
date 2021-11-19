<?php
include "config.inc.php";


/*****************************************************************************/

/**
  if no route:
    if not loggedin -> divs default -> SQL
    else -> user default ? divs default ? -> session ? -> SQL & init session

  if route:
    try to get route and run

*/

$route = '';

if (! isset($_REQUEST['r']) or ! $_REQUEST['r'] ) {
  # get division default_action for loggedin users

  if(DEBUGLEVEL > 0) \app::$logger->log(Psr\Log\LogLevel::DEBUG,"no r route");

  if(isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] === true){
    if(!empty($_SESSION['division']['default_action'])){
      $route = $_SESSION['division']['default_action'];
    }else{
      //TODO ???
      if ( function_exists('test') ) call_user_func("test");
    }
  }else{
    # redirect to frontpage
    if(DEBUGLEVEL > 0) \app::$logger->debug(' redirect to frontpage');
    if(!empty($_SESSION['division']['folder'])){
      if(DEBUGLEVEL > 0) \app::$logger->debug('folders/'.$_SESSION['division']['folder'].'/pub/');
      \app::redirect('folders/'.$_SESSION['division']['folder'].'/pub/');
    }else{
      header("HTTP/1.0 404 Not Found");
      die("<h2>404 Page or Module or Action not found...</h2>");
    }
  }

}else{
  $route = $_REQUEST['r'];
}
if(DEBUGLEVEL >= 3) \app::$logger->debug($route);

/**
 set up "global" variables
 */
# module path:  *\app::$basepath = __DIR__; *config
$dir = dirname($route);
app::$modulepath = app::$basepath.DS.'pro'.DS.$dir;
app::$module = basename($dir);
# get Model and its sql table
app::$modelClass = app::$module.'Model';
if(file_exists(app::$modulepath.DS.app::$modelClass.'.php')){
  app::$tableName = (app::$modelClass)::$tableName ?? app::$module;
  //\app::$logger->debug('[app::$tableName callable] '.app::$tableName);
}else{
  app::$tableName = app::$module;
  //\app::$logger->debug('[app::$tableName not callable] '.app::$modulepath.DS.app::$modelClass.'.php');
}
# get controller class ex: userController
app::$controllerClass = app::$module.'Controller';

//\app::$logger->debug('[app::$modelClass] '.app::$modelClass);
//\app::$logger->debug('[app::$controllerClass] '.app::$controllerClass);


# call action
\app::$action = basename($route);
$action = \app::$controllerClass.'::'.basename($route); //'\Controller::'.basename($_GET['r']);
//\app::$logger->log('error',$action);
//echo $action;
if(is_callable($action)) {
  # call controller::beforeAction hook -> permission checking etc
  if(is_callable(\app::$controllerClass.'::'.'beforeAction')){ 
    call_user_func(\app::$controllerClass.'::'.'beforeAction');
  }else {
    if(DEBUGLEVEL >= 0) \app::$logger->log('possible error: ',$action.' maybe not secure!');
  }
  # call user action :)
  $action();
}else{
  \app::$logger->debug(var_export($_SESSION['division'],true).$_REQUEST['r']);
  header("HTTP/1.0 404 Not Found");
  die("<h2>404 Page or Module or Action not found...</h2>");
}


?>