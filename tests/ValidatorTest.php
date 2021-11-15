<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
final class ValidatorTest extends TestCase
{
    private static $data = ['name' => 'Div name'];

    private static $ruleset = [
        'common' => [
            'name' => [
                'alphanumeric',
                ['minsize',4],
            ],//name
        ],// common
    ];

    private static $simpleruleset = [
        'common' => [
            'name' => 'alphanumeric',
        ],// common
    ];    

    public function testBasic(){
        $this->assertTrue( \bony\Validator::validate(self::$data, self::$ruleset) );
        self::$data['name'] = '???';
        $this->assertFalse( \bony\Validator::validate(self::$data, self::$ruleset) );

        $this->assertFalse( \bony\Validator::validate(self::$data, self::$simpleruleset) );

    }
}