<?php


declare( strict_types = 1 );


class MyFormattedLogger extends JDWX\App\FormattedLogger {


    public string $stWritten = '';


    protected function write( string $stMessage ) : void {
        $this->stWritten .= $stMessage;
    }


}
