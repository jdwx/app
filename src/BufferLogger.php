<?php


declare( strict_types = 1 );


namespace JDWX\App;


use Countable;
use Psr\Log\LoggerInterface;
use Stringable;


class BufferLogger implements LoggerInterface, Countable {


    use TRelayLogger;


    /** @var LogEntry[] */
    private array $rLogs = [];


    public function count() : int {
        return count( $this->rLogs );
    }


    public function empty() : bool {
        return empty( $this->rLogs );
    }


    public function log( $level, string|Stringable $message, array $context = [] ) : void {
        $this->rLogs[] = new LogEntry( $level, $message, $context );
    }


    public function shiftLog() : ?LogEntry {
        return array_shift( $this->rLogs );
    }


}
