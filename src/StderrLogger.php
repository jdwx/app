<?php


declare( strict_types = 1 );


namespace JDWX\App;


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;


class StderrLogger implements LoggerInterface {


    use TRelayLogger;


    /** @param array<string, mixed> $i_r */
    public static function formatArray( array $i_r, int $i_uIndent = 0 ) : string {
        $stIndent = str_repeat( ' ', $i_uIndent );
        $st = "{$stIndent}{\n";
        foreach ( $i_r as $stKey => $xValue ) {
            $st .= "{$stIndent}  {$stKey}: ";
            if ( is_array( $xValue ) ) {
                $st .= self::formatArray( $xValue, $i_uIndent + 2 );
            } else {
                $st .= $xValue;
            }
            $st .= "\n";
        }
        $st .= "{$stIndent}}\n";
        return $st;
    }


    /**
     * @inheritDoc
     */
    public function log( mixed $level, Stringable|string $message, array $context = [] ) : void {
        $stLevel = match ( $level ) {
            LOG_EMERG, LogLevel::EMERGENCY => 'EMERGENCY',
            LOG_ALERT, LogLevel::ALERT => 'ALERT',
            LOG_CRIT, LogLevel::CRITICAL => 'CRITICAL',
            LOG_ERR, LogLevel::ERROR => 'ERROR',
            LOG_WARNING, LogLevel::WARNING => 'WARNING',
            LOG_NOTICE, LogLevel::NOTICE => 'NOTICE',
            LOG_INFO, LogLevel::INFO => 'INFO',
            LOG_DEBUG, LogLevel::DEBUG => 'DEBUG',
            default => 'UNKNOWN',
        };
        $stMessage = $message instanceof Stringable ? $message->__toString() : $message;
        if ( ! empty( $context ) ) {
            if ( isset( $context[ 'class' ] ) ) {
                $stLevel .= '(' . $context[ 'class' ] . ')';
                unset( $context[ 'class' ] );
            }
            if ( isset( $context[ 'code' ] ) && $context[ 'code' ] === 0 ) {
                unset( $context[ 'code' ] );
            }
            $stMessage .= ' ' . static::formatArray( $context );
        }
        error_log( "{$stLevel}: {$stMessage}" );
    }


}
