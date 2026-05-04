<?php


declare( strict_types = 1 );


require_once __DIR__ . '/../vendor/autoload.php';


use JDWX\App\InteractiveApplication;
use JDWX\App\Term;


( new class( $argv ) extends InteractiveApplication {


    /** @noinspection PhpComposerExtensionStubsInspection */
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
        echo "You said: {$st}\n", Term::reset(), "Now try:\n";

        $stAltPrompt = Term::bold() . Term::color( Term::RED ) . 'AltPrompt> ' . Term::reset();
        $st = readline( $stAltPrompt );
        echo "You said: {$st}\n", Term::reset(), "Now try:\n";

        $stAltPrompt = Term::rlHack( Term::bold() . Term::color( Term::RED ), 1 ) . 'AltPrompt> ' . Term::rlHack( Term::reset(), 1 );
        $st = readline( $stAltPrompt );
        echo "You said: {$st}\n", Term::reset(), "Now try:\n";

        $stAltPrompt = Term::rlHack( Term::bold() . Term::color( Term::RED ), 2 ) . 'AltPrompt> ' . Term::rlHack( Term::reset(), 2 );
        $st = readline( $stAltPrompt );
        echo "You said: {$st}\n", Term::reset();

        return 0;
    }


} )();
