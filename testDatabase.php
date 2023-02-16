<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

final class DatabaseTester extends TestCase
{
    use TestCaseTrait;

    static private $pdo = null; // for test clean-up load.

    private $conn = null; // instantiate once per unit test.

    public function getConnection ()
    {
        if($this -> conn == null){
            if (self::$pdo == null){
                self::$pdo = new PDO('sqlite::memory:');
            }
            $this -> conn = $this -> createDefaultDBConnection(self::$pdo, ':memory:');
        }
        return $this -> conn;
    }

    public function testValidEmail (): void
    {
        $this -> assertInstanceOf(
            Email::class,
            Email::fromString('test123@gmail.com')
        );
    }

    public function testInvalidEmail (): void
    {
        $this -> expectException(InvalidArgumentException::class);
        Email::fromString('invalid');
    }

    public function testStringUsed(): void
    {
        $this -> assertEquals(
            'test123@gmail.com',
            Email::fromString('test123@gmail.com')
        );
    }

}
