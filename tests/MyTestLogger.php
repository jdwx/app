<?php


declare( strict_types = 1 );


use JDWX\App\TRelayLogger;
use Psr\Log\LoggerInterface;


class MyTestLogger implements LoggerInterface {


    use TRelayLogger;


    public ?int $level = null;
    public ?string $message = null;
    public ?array $context = null;


    public function log( $level, Stringable|string $message, array $context = [] ) : void {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }


}
