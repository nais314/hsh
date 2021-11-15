<?php
//use bony\ModelBase;
//include \app::$basepath.DIRECTORY_SEPARATOR.'pro'.DIRECTORY_SEPARATOR.'ModelBase.php';


class ledModel /* extends bony\ModelBase */ {
    public static $tableName = 'device_led';

    /**
    bony Validator properties ------------------------------------*/
    public static $ruleset;

}


/**
to add closures, functions, etc, (dynamic data), init must be separated */
function __construct(){
    ledModel::$ruleset = [

        'common' => [ #---scenario---
            'name' => [ #---property---
                'alphanumeric', # string: validator-function name (v_alphanumeric)
                'minlength' => 4, # 'fun name' => params | [param, param]
                # using non-assoc arrays was good for fast deserialization:
                //['maxlength',80], # array: fun-name,  params | [param, param], error-message
                'maxlength' => 32,



                /* ['filter_test',  function(&$value){ # <name discarded>, <function(&<var>)>
                    $value = 'FILTERED';
                    return true;
                }], */
            ],//name ---property---
        ],// common ---scenario---



        'insert' => [
            'name' => [ #---property---
                ['unique', function($value){ # <name discarded>, <function>
                    $field = 'name' ;
                    if(\app::$db->division()->where([$field=>$value])->rowCount()){
                        return "Name must be unique!";
                    }else{return true;}
                }],
            ],//name ---property---
        ],// insert ---scenario---




        # form input label's section:
        /**
        'visibility': public, private, readonly, masked */
        'properties' => [
            'name' => [
                'label'         => 'Name',
                'visibility'    => 'public',
                'display'       => 'text',
                'required'      => true,
                'autofocus'     => true,
            ],
            'gpio' => [
                'label'         => 'Gpio',
                'visibility'    => 'public',
                'display'       => 'text',
            ],
            'duty_cycle' => [
                'label'         => 'PWM duty cycle',
                'visibility'    => 'public',
                'display'       => 'text',
            ],
        ],// end properties



    ];// end ruleset............................

}
__construct();


