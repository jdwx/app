<?php


declare( strict_types = 1 );


require_once __DIR__ . '/../vendor/autoload.php';


use JDWX\App\InteractiveApplication;
use JDWX\App\Term;


( new class( $argv ) extends InteractiveApplication {


    /** @noinspection PhpComposerExtensionStubsInspection */
    protected function main() : int {
        $stPrompt = Term::color( Term::RED ) . 'Prompt>' . Term::reset() . ' ';
        echo $stPrompt, "AFTER!\n";
        $stPrompt = Term::bold() . $stPrompt;
        echo $stPrompt, "AFTER!\n";
        $this->setDefaultPrompt( $stPrompt );
        $st = $this->readLine();
        if ( $st === false ) {
            return 1;
        }
        echo "You said: {$st}\n", Term::reset(), "Now try:\n";

        $stPrompt = Term::bold() . Term::color( Term::RED ) . 'Prompt>' . Term::reset() . ' ';
        $st = \readline( $stPrompt );
        echo "You said: {$st}\n", Term::reset(), "Now try:\n";

        $stPrompt = Term::rlHack( Term::bold() . Term::color( Term::RED ), 1 ) . 'Prompt>' . Term::rlHack( Term::reset(), 1 ) . ' ';
        $st = readline( $stPrompt );
        echo "You said: {$st}\n", Term::reset(), "Now try:\n";

        $stPrompt = Term::rlHack( Term::bold() . Term::color( Term::RED ), 2 ) . 'Prompt>' . Term::rlHack( Term::reset(), 2 ) . ' ';
        $st = readline( $stPrompt );
        echo "You said: {$st}\n", Term::reset();

        return 0;
    }


} )();
