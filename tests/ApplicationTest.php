<?php


declare( strict_types = 1 );


use JDWX\App\BufferLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;


require_once __DIR__ . '/MyTestApplication.php';


class ApplicationTest extends TestCase {


    public function testArgs() : void {
        $app = new MyTestApplication([ 'test/command', 'foo', 'bar' ]);
        self::assertSame( 'foo', $app->args()->shiftStringEx() );
        self::assertSame( 'bar', $app->args()->shiftStringEx() );
    }


    public function testHandleOptions() : void {
        $app = new MyTestApplication([ 'test/command', '--foo=bar' ]);
        $app->run();
        self::assertSame( 'bar', $app->foo );
        $app = new MyTestApplication([ 'test/command', '--no-foo' ]);
        $app->run();
        self::assertSame( false, $app->foo );
        $app = new MyTestApplication([ 'test/command', '--bar' ]);
        $app->run();
        self::assertInstanceOf( InvalidArgumentException::class, $app->ex );
    }


    public function testHandleOptionsForValue() : void {
        $app = new MyTestApplication([ 'test/command', '--bar=baz' ]);
        $app->run();
        self::assertInstanceOf( InvalidArgumentException::class, $app->ex );
    }


    public function testLog() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication([ 'test/command' ], $log );
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
        $app = new MyTestApplication([ 'test/command' ], $log );
        $app->debug( 'TEST_MESSAGE' );
        self::assertCount( 0, $log );
    }


    public function testLogDebugForEnabled() : void {
        $log = new BufferLogger();
        $app = new MyTestApplication([ 'test/command', '--debug' ], $log );
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
        $app = new MyTestApplication([ 'test/command', '--debug=yes' ], $log );
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
        $app = new MyTestApplication([ 'test/command' ], $log );
        $rContext = [ 'foo' => 'bar' ];
        $app->info( 'TEST_MESSAGE_INFO', $rContext );
        self::assertCount( 1, $log );
        $log = $log->shiftLog();
        self::assertSame( LogLevel::INFO, $log->level );
        self::assertSame( 'TEST_MESSAGE_INFO', $log->message );
        self::assertSame( $rContext, $log->context );
    }


    public function testRun() : void {
        $st = new MyTestApplication([ 'test/command' ]);
        $st->run();
        self::assertSame( "command", $st->getCommand() );
        self::assertSame( "test/command", $st->getCommandPath() );
        self::assertSame( $st->iExitStatus, 0 );
    }


}
