<?php
    include '../pro/Validator.php';

    $data = ['name' => 'Div name'];

    $ruleset = [
        'common' => [
            'name' => [
                'alphanumeric',
                ['minsize',4],
                ['maxsize',80],
            ],//name
        ],// common
    ];

    $simpleruleset = [
        'common' => [
            'name' => 'alphanumeric',
        ],// common
    ];

     




    echo "<pre>";
    #------------------------------------------
    echo "OK? <br>";
    \bony\Validator::validate($data, $ruleset) ;

    print_r(\bony\Validator::getErrors());
    echo "<hr>";

    #------------------------------------------
    echo "FAIL? ['name'] = 'f ?'<br>";
    $data['name'] = 'f ?';
    \bony\Validator::validate($data, $ruleset) ;

    print_r(\bony\Validator::getErrors());
    echo "<hr>";

    #------------------------------------------
    echo "FAIL? ['name'] = '12345678901234567890123456789012345678901234567890123456789012345678901234567890'<br>";
    $data['name'] = '12345678901234567890123456789012345678901234567890123456789012345678901234567890';
    \bony\Validator::validate($data, $ruleset) ;

    print_r(\bony\Validator::getErrors());
    echo "<hr>";

    #------------------------------------------
    echo "FAIL? ['name'] = 'f ?'<br>";
    $data['name'] = 'f ?';
    \bony\Validator::validate($data, $simpleruleset) ;

    print_r(\bony\Validator::getErrors());
    echo "<hr>";

    #------------------------------------------    

    $ruleset = [
        'common' => [
            'name' => [
                ['alphanumeric',"kabbe"],
                ['minsize',4,"na ugye?!"],
                ['maxsize',80],
            ],//name
        ],// common
    ]; 
    echo "FAIL? ['name'] = 'f ?'<br>";
    $data['name'] = 'f ?';
    \bony\Validator::validate($data, $ruleset) ;

    print_r(\bony\Validator::getErrors());
    echo "<hr>";

    #------------------------------------------
    echo "FAIL? unique ['name'] = 'alma'<br>";
    $data['name'] = 'alma';
    chdir('..');
    include 'config.inc.php';

    $ruleset = [
        'common' => [
            'name' => [
                'alphanumeric',
                ['minsize',4],
                ['maxsize',80],

                ['unique', function($value){
                    $field = 'name' ;
                    if(\app::$db->division()->where([$field=>$value])->rowCount()){
                        return "Name must be unique!";
                    }else{return true;}
                }],
            ],//name
        ],// common
    ];

    \bony\Validator::validate($data, $ruleset) ;

    print_r(\bony\Validator::getErrors());
    echo "<hr>";

    #------------------------------------------