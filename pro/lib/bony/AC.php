<?php
namespace bony;

class AC {
    /**
    functions by app profile:
    Level-I: allowLoggedIn
    or
    class *Controller {
        private static $ac_rules = 
        [
            'common'=>[
                '*'=>['loggedin'],
            ],
    ...............................
    Level-2: VM (View or Modify)
    allow logged-in user to view only or full access to modules
    class *Controller {
        private static $ac_rules = 
        [
            'common'=>[
                'view'=>['loggedin'],            
                'update'=>[
                    'allow_user'=>['user1', 'user2'],
                    'allow_role'=>['role1','role2']
                ],    
    ...............................
    Level-III: CRBAC
    CRBAC module
    use per Division RBAC-CRUD permission table
    use per User RBAC-CRUD permission table

    v_check_CRBAC()

        private static $ac_rules = 
        [
            'common'=>[
                'view'  => 'v_check_CRBAC',            
                'update'=> 'v_check_CRBAC',    
    ...............................
    Level-4: OCRBAC
    CRBAC module
    use per Division RBAC-CRUD permission table
    use per User RBAC-CRUD permission table
    use per Document permission table
    optional: use 2 way authentication for document access
        private static $ac_rules = 
        [
            'common'=>[
                'view'  => 'v_check_OCRBAC',            
                'update'=> 'v_check_OCRBAC',    
    */





    /**
    shortcut functions--------------------------------------------------*/
    public static function allowLoggedIn($return=false){
        $ruleset = [ 'common'=>[
                        'user'=>['loggedin'],
                    ], ];

        $adat = ['user' => null]; # only data key needed, it will be searched in rules

        $validation_result = \bony\Validator::validate(
            $adat, 
            $ruleset,
            $scenario = $_SESSION['scenario'], 
            $validator_className='bony\AccessValidators');

        if($validation_result === true ){
            return true;
        }else{
            $error = 'ACCESS DENIED';
            include \app::layout('error');
            die();
        }
    }

    #role based
    public static function allowAdmin(){

    }

    public static function allowSuper(){

    }    

    public static function allowMember(){

    }

    public static function allowLocalhost(){

    }

    public static function allowLAN(){

    }
    
    /**
    default redirect function on deny - equals to deny() ---------------*/
    public static function deny(){

    }    

    /**
    complex rule check -------------------------------------------------
    it is a wrapper around Validator*/
    public static function check($ruleset=null, 
    /* $scenario='common', */ $validator_className='bony\AccessValidators',
    $return=false ){
        # parser will search for 'key' in rules array:
        # filters are enabled, so i create a new variable (plus: 1 less "if" in validate())
        $adat = [\app::$action => ''];

        $validation_result = \bony\Validator::validate(
            $adat, 
            $ruleset,
            #const:
            $scenario=$_SESSION['scenario'], 
            $validator_className='bony\AccessValidators');

        //var_dump($validation_result);
        if($validation_result === true ){
            return true;
        }else{
            if(!$return){
                //echo "<script>document.location.href='https://google.hu'</script>";
                $error = 'ACCESS DENIED';
                include \app::layout('error');
                die();

            }else return false;
        }

    }//--------------------------------------------------------------    

}