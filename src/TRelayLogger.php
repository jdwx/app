<?php


declare( strict_types = 1 );


namespace JDWX\App;


use JDWX\Log\RelayLoggerTrait;


/**
 * @deprecated Use JDWX\Log\RelayLoggerTrait
 * @suppress PhanDeprecatedTrait
 *
 * Retain until 1.2.0
 */
trait TRelayLogger {


    /** @noinspection PhpDeprecationInspection */
    use RelayLoggerTrait;
}

