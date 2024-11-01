<?php


declare( strict_types = 1 );


namespace JDWX\App;


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;


class StderrLogger implements LoggerInterface {


    use TRelayLogger;


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
            $stJson = json_encode( $context, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_IGNORE | JSON_THROW_ON_ERROR );
            $stMessage .= " {$stJson}";
        }
        error_log( "{$stLevel}: {$stMessage}" );
    }


}
