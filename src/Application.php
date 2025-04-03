<?php


declare( strict_types = 1 );


namespace JDWX\App;


use Exception;
use InvalidArgumentException;
use JDWX\Args\Arguments;
use JDWX\Args\BadArgumentException;
use JDWX\Args\ExtraArgumentsException;
use JDWX\Args\MissingArgumentException;
use JDWX\Log\StderrLogger;
use JDWX\Param\Parse;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;


abstract class Application implements LoggerInterface {


    /** @noinspection PhpUnused */
    public const EXIT_SUCCESS = 0;

    public const EXIT_FAILURE = 1;


    use LoggerTrait;


    protected bool $bDebug = false;

    protected int $pid;

    protected string $stCommand;

    protected string $stCommandPath;

    private Arguments $args;

    private readonly LoggerInterface $log;


    /** @param string[]|Arguments|null $i_argv */
    public function __construct( array|Arguments|null $i_argv = null, ?LoggerInterface $log = null ) {
        global $argv;
        $this->log = $log ?? new StderrLogger();
        $this->pid = getmypid();
        if ( $i_argv instanceof Arguments ) {
            $this->args = $i_argv;
        } else {
            $this->args = $this->newArguments( $i_argv ?? $argv );
        }
        $this->stCommandPath = $this->args->shiftStringEx();
        $this->stCommand = basename( $this->stCommandPath );
    }


    /** @return array<string, mixed> */
    public static function throwableToArray( Throwable $i_throwable,
                                             bool      $i_bIncludeTrace = true ) : array {
        $r = [
            'class' => $i_throwable::class,
            'message' => $i_throwable->getMessage(),
            'code' => $i_throwable->getCode(),
            'file' => $i_throwable->getFile(),
            'line' => $i_throwable->getLine(),
        ];
        if ( $i_bIncludeTrace ) {
            $r[ 'backtrace' ] = $i_throwable->getTrace();
        }
        $x = $i_throwable->getPrevious();
        if ( $x ) {
            $r[ 'previous' ] = self::throwableToArray( $x, $i_bIncludeTrace );
        }
        return $r;
    }


    public function args() : Arguments {
        return $this->args;
    }


    public function getCommand() : string {
        return $this->stCommand;
    }


    public function getCommandPath() : string {
        return $this->stCommandPath;
    }


    public function handleOption( string $i_stOption, bool|string $i_bstValue ) : void {
        $method = 'handleOption_' . strtolower( $i_stOption );
        if ( method_exists( $this, $method ) ) {
            $this->$method( $i_bstValue );
            return;
        }
        if ( $i_bstValue === true || $i_bstValue === false ) {
            throw new InvalidArgumentException( "Unknown option \"{$i_stOption}\"" );
        }
        throw new InvalidArgumentException( "Unknown option \"{$i_stOption}" . ( $i_bstValue ? "({$i_bstValue})" : '' ) . "\"" );
    }


    /**
     * @noinspection PhpMethodNamingConventionInspection
     * @noinspection PhpUnused
     */
    public function handleOption_debug( bool|string $i_bDebug ) : void {
        if ( ! is_bool( $i_bDebug ) ) {
            $i_bDebug = Parse::bool( $i_bDebug );
        }
        $this->bDebug = $i_bDebug;
    }


    public function handleOptions() : void {
        $rOptions = $this->args->handleOptions();
        foreach ( $rOptions as $stOption => $stValue ) {
            $this->handleOption( $stOption, $stValue );
        }
    }


    public function log( mixed $level, string|Stringable $message, array $context = [] ) : void {
        if ( ( LogLevel::DEBUG === $level || LOG_DEBUG === $level ) && ! $this->bDebug ) {
            return;
        }
        $this->log->log( $level, $message, $context );
    }


    /**
     * @param mixed[] $i_rContext
     * @deprecated Use debug() from LoggerInterface. Preserve until 1.1.
     * @noinspection PhpUnused
     * @suppress PhanDeprecatedFunction
     */
    public function logDebug( string $i_stMessage, array $i_rContext = [] ) : void {
        $this->log( LOG_DEBUG, $i_stMessage, $i_rContext );
    }


    /**
     * @param mixed[] $i_rContext
     * @deprecated Use error() from LoggerInterface. Preserve until 1.1.
     * @noinspection PhpUnused
     * @suppress PhanDeprecatedFunction
     */
    public function logError( string $i_stMessage, array $i_rContext = [] ) : void {
        $this->log( LOG_ERR, $i_stMessage, $i_rContext );
    }


    /**
     * @param mixed[] $i_rContext
     * @deprecated Use info() from LoggerInterface. Preserve until 1.1.
     * @suppress PhanDeprecatedFunction
     * @noinspection PhpUnused
     */
    public function logInfo( string $i_stMessage, array $i_rContext = [] ) : void {
        $this->log( LOG_INFO, $i_stMessage, $i_rContext );
    }


    /**
     * @param mixed[] $i_rContext
     * @deprecated Use warning() from LoggerInterface. Preserve until 1.1.
     * @suppress PhanDeprecatedFunction
     * @noinspection PhpUnused
     */
    public function logWarning( string $i_i_stMessage, array $i_rContext = [] ) : void {
        $this->log( LOG_WARNING, $i_i_stMessage, $i_rContext );
    }


    final public function run() : void {
        try {
            $this->setup();
            $this->handleOptions();
            $this->debug( 'application begins' );
            if ( $this->bDebug ) {
                $this->debugSetup();
            }
            $rc = $this->main();
            if ( $this->bDebug ) {
                $this->debugCleanup();
            }
            $this->debug( "application ends with {$rc}" );
            flush();
            $this->exit( $rc );
        } catch ( Exception $ex ) {
            $ni = $this->handleException( $ex );
            if ( is_int( $ni ) ) {
                $this->exit( $ni );
            }
        }
    }


    public function setup() : void {}


    protected function debugCleanup() : void {}


    protected function debugSetup() : void {}


    /**
     * In testing, this function is overridden to capture the exit status,
     * so it does not actually exit.
     *
     * @noinspection PhpNoReturnAttributeCanBeAddedInspection
     */
    protected function exit( int $i_iStatus ) : void {
        exit( $i_iStatus );
    }


    /**
     * @return ?int The desired exit status. If null, the exit() method
     *              will not be called and execution will continue in the
     *              calling script below the original call to the run() method.
     */
    protected function handleException( Exception $i_ex ) : ?int {
        $r = static::throwableToArray( $i_ex, i_bIncludeTrace: false );
        $stMessage = $r[ 'message' ];
        unset( $r[ 'message' ] );
        if ( $i_ex instanceof InvalidArgumentException || $i_ex instanceof MissingArgumentException ) {
            // Do nothing.
        } elseif ( $i_ex instanceof BadArgumentException ) {
            $r[ 'value' ] = $i_ex->getValue();
        } elseif ( $i_ex instanceof ExtraArgumentsException ) {
            $r[ 'extra' ] = $i_ex->getArguments();
        } else {
            # Redo it to get the stack trace.
            $r = static::throwableToArray( $i_ex );
        }
        $this->error( $stMessage, $r );
        return static::EXIT_FAILURE;
    }


    abstract protected function main() : int;


    /** @param list<string> $i_argv */
    protected function newArguments( array $i_argv ) : Arguments {
        return new Arguments( $i_argv );
    }


}
