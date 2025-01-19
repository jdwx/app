<?php


declare( strict_types = 1 );


namespace JDWX\App;


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;


abstract class FormattedLogger implements LoggerInterface {


    use TRelayLogger;


    /** @param array<string, mixed> $i_r */
    public static function formatArray( array $i_r ) : string {
        return self::formatArrayInner( $i_r, 0 );
    }


    /**
     * @param array<string, mixed> $i_r
     * @param int $i_uIndent The number of spaces to indent nested arrays. (Internal.)
     * @param list<mixed[]|object> $i_rAlreadySeen Objects that have already been printed. (Internal.)
     * @return string The formatted string representation of the array.
     */
    private static function formatArrayInner( array $i_r, int $i_uIndent,
                                              array &$i_rAlreadySeen = [] ) : string {
        $stIndent = str_repeat( ' ', $i_uIndent );
        $st = "{\n";
        foreach ( $i_r as $stKey => $xValue ) {
            $st .= "{$stIndent}  {$stKey}: ";
            if ( is_array( $xValue ) ) {
                if ( in_array( $xValue, $i_rAlreadySeen, true ) ) {
                    $st .= "array (already printed)\n";
                } else {
                    $i_rAlreadySeen[] = $xValue;
                    $st .= 'array ' . self::formatArrayInner( $xValue, $i_uIndent + 2, $i_rAlreadySeen );
                }
            } elseif ( is_object( $xValue ) ) {
                if ( in_array( $xValue, $i_rAlreadySeen, true ) ) {
                    $st .= get_class( $xValue ) . " (already printed)\n";
                } else {
                    $i_rAlreadySeen[] = $xValue;
                    $st .= get_class( $xValue ) . ' ' . self::formatArrayInner( (array) $xValue, $i_uIndent + 2, $i_rAlreadySeen );
                }
            } else {
                $st .= $xValue;
                $st .= "\n";
            }
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
        $this->write( "{$stLevel}: {$stMessage}" );
    }


    abstract protected function write( string $stMessage ) : void;


}
