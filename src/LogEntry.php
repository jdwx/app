<?php


declare( strict_types = 1 );


namespace JDWX\App;


use InvalidArgumentException;
use Psr\Log\LogLevel;
use Stringable;


readonly class LogEntry {


    public string $level;
    public string $message;
    public array $context;


    public function __construct( int|string $i_level, string|Stringable $i_stMessage, array $i_rContext ) {
        $this->level = match( $i_level ) {
            LogLevel::EMERGENCY, LOG_EMERG => LogLevel::EMERGENCY,
            LogLevel::ALERT, LOG_ALERT => LogLevel::ALERT,
            LogLevel::CRITICAL, LOG_CRIT => LogLevel::CRITICAL,
            LogLevel::ERROR, LOG_ERR => LogLevel::ERROR,
            LogLevel::WARNING, LOG_WARNING => LogLevel::WARNING,
            LogLevel::NOTICE, LOG_NOTICE => LogLevel::NOTICE,
            LogLevel::INFO, LOG_INFO => LogLevel::INFO,
            LogLevel::DEBUG, LOG_DEBUG => LogLevel::DEBUG,
            default => throw new InvalidArgumentException( "Invalid log level: {$i_level}" ),
        };
        $this->message = strval( $i_stMessage );
        $this->context = $i_rContext;
    }


}
