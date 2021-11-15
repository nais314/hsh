<?php

    class siteController {

        /**
        retrieve auth data, and authenticate user------------------------------*/


        /** 
        LEVEL-I authentication */
/*         private function authenticate(){
            $users = [
                'pi' => '$2y$10$qBV8ZrNvLjs8aOQUxdcNyufrQ9TGHKtQCbp/QhQhFLmsH20cDl3RG',
            ];


            if(
                password_verify( $_POST['user']['password'], $users[ $_POST['user']['name'] ] )
            )   return true;

            return false;    
        } */

        /** 
        LEVEL-2 authentication with LessQL */
/*         private function authenticate(){

            $result = app::$db->user()->select( 'password' )->where('name', $_POST['user']['name']);
            $row = $result->fetch();

            if(
                password_verify( $_POST['user']['password'], $row['password'] )
            )   return true;

            return false;    
        } */

        /** 
        LEVEL-III authentication */
        private function authenticate(){
            //\app::$logger->debug(__METHOD__.var_export(app::$db,true));

            /* $result = app::$db->user()->select( 'password' )
                ->where('name', $_POST['user']['name'])
                ->where('division_id', $_SESSION['division']['id']);
            $row = $result->fetch();
            //\app::$logger->debug(__METHOD__.var_export($row,true));
            if(
                password_verify( $_POST['user']['password'], $row['password'] )
            )  */ 


            $test = \bony\resql::fetch("name=?", $_POST['user']['name'], 'user', 'password');
            if(
                password_verify( $_POST['user']['password'], $test['password'] )
            ){
                app::$logger->log('DEBUG',
                    "authenticate: ".
                    $_POST['user']['name']
                );

                return true;
            }else{
                
                app::$logger->log('DEBUG',
                    "authenticate: Wrong Username or Password: ".
                    $_POST['user']['name']
                );

                return false;
            }


/*  return true;

            //var_export($row); */
            return false;    
        }            


        /** 
        login, uses auth functions -----------------------------------------*/

        public function login(){
            if(isset($_SESSION['loggedIn'])){
                \app::redirect('?r=site/logout');
            };
            //unset($_SESSION['loggedIn']);
            //unset($_SESSION['token']);

            # same function for form view and submitted data processing
            # if form submitted:
            if(isset($_POST['user'])){

                //$validation_result = bony\Validator::validate($_POST['user'], divisionModel::$ruleset) ;

                if(self::authenticate()){
                    //$content = "<h1>OK</h1>";
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['token'] = \app::genId(32);
                    /**
                    get user data for Level-2 app (RBAC) ---*/
                    $_SESSION['user'] = \bony\resql::fetch(
                        "name = ? AND division_id = ?", 
                        [$_POST['user']['name'],  (int)$_SESSION['division']['id']],
                        'user'
                    );
                    \app::$logger->debug(__METHOD__." [user]: ".var_export($_SESSION['user'],true));

                    if(isset($_SESSION['QUERY_STRING'])
                        && !empty($_SESSION['QUERY_STRING']))
                    {
                        \app::redirect($_SESSION['QUERY_STRING']);
                    }elseif(isset($_SESSION['division']['default_action'])
                        && !empty($_SESSION['division']['default_action'])
                    ){
                        \app::redirect('?r='.$_SESSION['division']['default_action']);
                    }
                    //---------------------------------------

                }else{
                    $content = "<h1>LOGIN ERROR</h1>";
                    unset($_SESSION['loggedIn']);
                };

            # if first run:
            }else{
                $content = \app::render('_login_form');
            }

            #redirect to defalt or caller?
            include \app::layout('min_layout');
        }//_____________________________________________________________________

       /*  function view($id = 0){
            

            if (isset($_GET['id'])) $id = $_GET['id'];

            $row = \app::$db->division(['id'=>$id]);
            $data = $row->getData();


            $content = "<h1>{\app::$module}</h1><pre>" .var_export($data,true). "</pre>";
		
		    include \app::$basepath.'/pro/view/layout.php';
        } */

        public static function logout(){
            //$_SESSION = [];
            session_unset();
            session_destroy();
            \app::redirect('?r=site/login');
        }//_____________________________________________________________________




        function index(){
            
            $content = "<h1>Index Page</h1>";

            $content .= <<<EOT
            <h3>TODOs</h3>
            <ul>
                <li>TOKENS! - mostly done</li>

                <li>module_menu</li>
                
                <li>
                // switch on tree_root type?
                xhttp.open("GET", "?r="+tree_ro
                </li>

                <li>
                ti activate function ?
                </li>                

                <li>
                NEW: tree view<br>
                division/tree - how to generate tree with ADD REMOVE etc btns?
                </li>

                <li>CSS tree LIGHT vs DARK ? can be generalised for reuse ???
                </li>

                <li>
                Documentation? Github?
                </li>
            </ul>
EOT;
		
		    include \app::$basepath.'/pro/view/layout.php';
        }//_____________________________________________________________________








        public function test(){
            $content = "<h1>Test Page</h1>";

            /* $rows = \bony\resql::fetchAll(['id'=>[1,2,3,4,5]],null,'page');

            $content .= var_export($rows,true); */

            $content = simga::init(
                $albumpath = $_SESSION['division']['folder'].DS.'albums',
                $JSLoader = '\app::add_JSFile', // interface for frameworks = function($filename)
                $CSSLoader = '\app::add_CSSFile', // interface for frameworks = function($filename)
                $logger = '\app::$logger'
            );

            include \app::layout();

        }//_____________________________________________________________________


    }//end class________________________________________________________________