<?php


declare( strict_types = 1 );


require_once __DIR__ . '/../vendor/autoload.php';


use JDWX\App\InteractiveApplication;
use JDWX\App\Term;


( new class( $argv ) extends InteractiveApplication {


    protected function main() : int {
        $stPrompt = Term::color( Term::RED ) . 'Prompt> ' . Term::reset();
        echo $stPrompt, "AFTER!\n";
        $stPrompt = Term::bold() . $stPrompt;
        echo $stPrompt, "AFTER!\n";
        $this->setDefaultPrompt( $stPrompt );
        $st = $this->readLine();
        if ( $st === false ) {
            return 1;
        }
        echo "You said: {$st}\n";
        return 0;
    }


} )();
