<?php


declare( strict_types = 1 );


namespace JDWX\App;


use Stringable;


trait TRelayLogger {


    public function alert( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_ALERT, $message, $context );
    }


    public function critical( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_CRIT, $message, $context );
    }


    public function debug( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_DEBUG, $message, $context );
    }


    public function emergency( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_EMERG, $message, $context );
    }


    public function error( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_ERR, $message, $context );
    }


    public function info( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_INFO, $message, $context );
    }


    /**
     * The actual type of $level in LoggerInterface::log() is mixed, but we don't need
     * that here. That'll be enforced by implementing LoggerInterface for a class that
     * uses this trait.
     */
    abstract public function log( int $level, Stringable|string $message, array $context = [] ) : void;


    public function notice( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_NOTICE, $message, $context );
    }


    public function warning( Stringable|string $message, array $context = [] ) : void {
        $this->log( LOG_WARNING, $message, $context );
    }


}
