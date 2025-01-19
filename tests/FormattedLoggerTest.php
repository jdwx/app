<?php


declare( strict_types = 1 );


use JDWX\App\StderrLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


require_once __DIR__ . '/MyFormattedLogger.php';


final class FormattedLoggerTest extends TestCase {


    public function testFormatArray() : void {
        $result = StderrLogger::formatArray( [ 'message' => 'TEST_MESSAGE', 'foo' => 'bar' ] );
        self::assertStringContainsString( 'TEST_MESSAGE', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
    }


    public function testFormatArrayForArrayArrayLoop() : void {
        $x = [ 'foo' => 'bar' ];
        $r = [ 'x' => & $x, 'baz' => 'qux' ];
        $x[ 'r' ] = $r;
        $result = StderrLogger::formatArray( $x );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForArrayObjectLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $r = [ 'x' => $x, 'baz' => 'qux' ];
        $x->r = $r;
        $result = StderrLogger::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForNestedArray() : void {
        $result = StderrLogger::formatArray( [ 'message' => 'TEST_MESSAGE', 'foo' => [ 'bar' => 'baz' ] ] );
        self::assertStringContainsString( 'TEST_MESSAGE', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
    }


    public function testFormatArrayForObject() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $result = StderrLogger::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
    }


    public function testFormatArrayForObjectArrayLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $r = [ 'x' => $x ];
        $x->r = $r;
        $result = StderrLogger::formatArray( $r );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testFormatArrayForObjectObjectLoop() : void {
        $x = new stdClass();
        $x->foo = 'bar';
        $y = new stdClass();
        $y->baz = 'qux';
        $x->y = $y;
        $y->x = $x;
        $result = StderrLogger::formatArray( [ 'x' => $x ] );
        self::assertStringContainsString( 'stdClass', $result );
        self::assertStringContainsString( 'foo', $result );
        self::assertStringContainsString( 'bar', $result );
        self::assertStringContainsString( 'baz', $result );
        self::assertStringContainsString( 'qux', $result );
        self::assertStringContainsString( 'already printed', $result );
    }


    public function testLog() : void {
        $logger = new MyFormattedLogger();
        $logger->log( LogLevel::WARNING, 'TEST_MESSAGE', [
            'class' => 'TEST_CLASS',
            'code' => 0,
        ] );
        self::assertStringContainsString( 'WARNING', $logger->stWritten );
        self::assertStringContainsString( 'TEST_MESSAGE', $logger->stWritten );
        self::assertStringContainsString( 'TEST_CLASS', $logger->stWritten );
        self::assertStringNotContainsString( '0', $logger->stWritten );
    }


}
