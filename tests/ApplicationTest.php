<?php


declare( strict_types = 1 );


use JDWX\Args\Arguments;
use JDWX\Args\BadArgumentException;
use JDWX\Args\ExtraArgumentsException;
use JDWX\Log\BufferLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


require_once __DIR__ . '/MyTestApplication.php';


class ApplicationTest extends TestCase {


    public function testArgs() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', 'foo', 'bar' ], $logger );
        self::assertSame( 'foo', $app->args()->shiftStringEx() );
        self::assertSame( 'bar', $app->args()->shiftStringEx() );
        self::assertSame( 0, $logger->count() );
    }


    public function testBadArgument() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', 'foo' ], $logger );
        $app->fnCallback = static function () {
            throw new BadArgumentException( 'foo', 'TEST_MESSAGE' );
        };
        $app->run();
        $log = $logger->shiftLog();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( 'TEST_MESSAGE', $log->message );
        self::assertSame( BadArgumentException::class, $log->context[ 'class' ] );
        self::assertSame( 'foo', $log->context[ 'value' ] );
    }


    public function testExitStatus() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command' ], $logger );
        $app->niMainExitStatus = 234567;
        $app->run();
        self::assertSame( 234567, $app->niObservedExitStatus );
    }


    public function testExtraArguments() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', 'foo', 'bar' ], $logger );
        $app->fnCallback = static function () {
            throw new ExtraArgumentsException( [ 'foo' ], 'TEST_MESSAGE' );
        };
        $app->run();
        $log = $logger->shiftLog();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( 'TEST_MESSAGE', $log->message );
        self::assertSame( ExtraArgumentsException::class, $log->context[ 'class' ] );
        self::assertSame( [ 'foo' ], $log->context[ 'extra' ] );
    }


    public function testHandleException() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'fake_command' ], $logger );
        $app->fnCallback = static function () {
            throw new InvalidArgumentException( 'TEST_MESSAGE' );
        };
        $app->run();
        $log = $logger->shiftLog();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( 'TEST_MESSAGE', $log->message );
        self::assertSame( InvalidArgumentException::class, $log->context[ 'class' ] );
    }


    public function testHandleExceptionForExitStatus() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'fake_command' ], $logger );
        $app->niErrorExitStatus = 123456;
        $app->fnCallback = static function () {
            throw new InvalidArgumentException( 'TEST_MESSAGE', 1 );
        };
        $app->run();
        self::assertSame( 123456, $app->niObservedExitStatus );
    }


    public function testHandleOptions() : void {
        $app = new MyTestApplication( [ 'test/command', '--foo=bar' ] );
        $app->run();
        self::assertSame( 'bar', $app->foo );
        $app = new MyTestApplication( [ 'test/command', '--no-foo' ] );
        $app->run();
        self::assertFalse( $app->foo );
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', '--bar' ], $logger );
        $app->run();
        $log = $logger->shiftLog();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( InvalidArgumentException::class, $log->context[ 'class' ] );
    }


    public function testHandleOptionsForValue() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', '--bar=baz' ], $logger );
        $app->run();
        $log = $logger->shiftLog();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( InvalidArgumentException::class, $log->context[ 'class' ] );
    }


    public function testInvoke() : void {
        $st = new MyTestApplication( [ 'test/command' ] );
        $st();
        self::assertSame( 'command', $st->getCommand() );
        self::assertSame( 'test/command', $st->getCommandPath() );
        self::assertSame( 0, $st->niObservedExitStatus );
    }


    public function testLog() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command' ], $log );
        $rContext = [ 'foo' => 'bar' ];
        $app->warning( 'TEST_MESSAGE', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLog();
        self::assertSame( LogLevel::WARNING, $log->level );
        self::assertSame( 'TEST_MESSAGE', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testLogDebugForDisabled() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command' ], $log );
        $app->debug( 'TEST_MESSAGE' );
        self::assertCount( 0, $log );
    }


    public function testLogDebugForEnabled() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', '--debug' ], $log );
        $app->run();
        $app->debug( 'TEST_MESSAGE' );
        self::assertCount( 3, $log );
        $log->shiftLog(); # Skip the first two logs
        $log->shiftLog();
        $log = $log->shiftLog();
        self::assertSame( LogLevel::DEBUG, $log->level );
        self::assertSame( 'TEST_MESSAGE', $log->message );
        self::assertSame( [], $log->context );
    }


    public function testLogDebugForEnabledExplicitly() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command', '--debug=yes' ], $log );
        $app->run();
        $app->debug( 'TEST_MESSAGE_DEBUG' );
        self::assertCount( 3, $log );
        $log->shiftLog(); # Skip the first two logs, which are "begins" and "ends" from Application::run().
        $log->shiftLog();
        $log = $log->shiftLog();
        self::assertSame( LogLevel::DEBUG, $log->level );
        self::assertSame( 'TEST_MESSAGE_DEBUG', $log->message );
        self::assertSame( [], $log->context );
    }


    public function testLogInfo() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command' ], $log );
        $rContext = [ 'foo' => 'bar' ];
        $app->info( 'TEST_MESSAGE_INFO', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLog();
        self::assertSame( LogLevel::INFO, $log->level );
        self::assertSame( 'TEST_MESSAGE_INFO', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testPreMadeArguments() : void {
        $logger = new BufferLogger();
        $args = new Arguments( [ 'fake_command', 'foo', 'bar' ] );
        $app = new MyTestApplication( $args, $logger );
        self::assertSame( 'foo', $app->args()->shiftStringEx() );
        self::assertSame( 'bar', $app->args()->shiftStringEx() );
    }


    public function testRun() : void {
        $st = new MyTestApplication( [ 'test/command' ] );
        $st->run();
        self::assertSame( 'command', $st->getCommand() );
        self::assertSame( 'test/command', $st->getCommandPath() );
        self::assertSame( 0, $st->niObservedExitStatus );
    }


    public function testRuntimeException() : void {
        $logger = new BufferLogger();
        $app = new MyTestApplication( [ 'test/command' ], $logger );
        $app->fnCallback = static function () {
            throw new RuntimeException( 'TEST_MESSAGE' );
        };
        $app->run();
        $log = $logger->shiftLog();
        self::assertSame( LogLevel::ERROR, $log->level );
        self::assertSame( 'TEST_MESSAGE', $log->message );
        self::assertSame( RuntimeException::class, $log->context[ 'class' ] );
        self::assertArrayHasKey( 'backtrace', $log->context );
    }


    public function testThrowableToArray() : void {
        try {
            throw new InvalidArgumentException( 'TEST_MESSAGE' );
        } catch ( Throwable $ex ) {
            $r = MyTestApplication::throwableToArray( $ex );
            self::assertSame( 'InvalidArgumentException', $r[ 'class' ] );
            self::assertSame( 'TEST_MESSAGE', $r[ 'message' ] );
            self::assertSame( __FILE__, $r[ 'file' ] );
            self::assertSame( __LINE__ - 6, $r[ 'line' ] );
        }
    }


    public function testThrowableToArrayWithPrevious() : void {
        try {
            try {
                throw new InvalidArgumentException( 'TEST_MESSAGE_PREVIOUS', 1 );
            } catch ( Throwable $ex ) {
                throw new RuntimeException( 'TEST_MESSAGE', 2, $ex );
            }
        } catch ( Throwable $ex ) {
            $r = MyTestApplication::throwableToArray( $ex );
            self::assertSame( 'RuntimeException', $r[ 'class' ] );
            self::assertSame( 'TEST_MESSAGE', $r[ 'message' ] );
            self::assertSame( 2, $r[ 'code' ] );
            self::assertSame( __FILE__, $r[ 'file' ] );
            self::assertSame( __LINE__ - 8, $r[ 'line' ] );
            self::assertSame( 'InvalidArgumentException', $r[ 'previous' ][ 'class' ] );
            self::assertSame( 'TEST_MESSAGE_PREVIOUS', $r[ 'previous' ][ 'message' ] );
            self::assertSame( 1, $r[ 'previous' ][ 'code' ] );
            self::assertSame( __FILE__, $r[ 'previous' ][ 'file' ] );
            self::assertSame( __LINE__ - 15, $r[ 'previous' ][ 'line' ] );
        }
    }


}
