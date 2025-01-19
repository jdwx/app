<?php


declare( strict_types = 1 );


use JDWX\App\Application;
use JDWX\App\Term;


require_once __DIR__ . '/../vendor/autoload.php';


( new class( $argv ) extends Application {


    protected function main() : int {
        echo Term::clear(), Term::home();
        echo Term::textForeground( 'Hello, World!', Term::GREEN ), "\n";
        echo Term::textBackground( 'Hello, World!', Term::RED ), " \n";
        echo Term::textColor( 'Hello, World!', Term::GREEN, Term::RED ), "\n";
        echo Term::textBold( 'Hello, World!' ), "\n";
        echo Term::color( Term::BLUE, Term::WHITE ), Term::bold(), 'Hello, World!', Term::reset(), "\n";
        echo Term::reverse(), 'Hello, World!', Term::reset(), "\n";
        echo Term::textUnderline( 'Hello, World!' ), "\n";
        echo Term::textItalic( 'Hello, World!' ), "\n";
        echo Term::textStrike( 'Hello, World!' ), "\n";
        echo Term::textBlink( 'Hello, World!' ), "\n";
        echo Term::right( 3 ), 'Hello, World!', Term::left( 3 ), "Hello, world!\n";
        echo Term::save(), Term::pos( 5, 20 ), 'Hello, World!', Term::restore(), "Like nothing happened.\n";
        echo '       World!', Term::column( 1 ), "Hello,\n";
        echo "Normal.\n";
        return 0;
    }


} )->run();
