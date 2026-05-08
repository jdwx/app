<?php


declare( strict_types = 1 );


use JDWX\App\Term;


require_once __DIR__ . '/../vendor/autoload.php';


(function( array $argv ) : void {

    array_shift( $argv );
    $stTitle = implode( ' ', $argv );
    echo Term::title( $stTitle );

})( $argv ?? [] );