<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\App;


use JDWX\Args\Arguments;
use LogicException;
use Psr\Log\LoggerInterface;


/**
 * This class provides a base for interactive applications that depend
 * heavily on the readline extension.
 *
 * Part of the motivation for this class is that code that uses readline
 * is extremely difficult to test.  This class provides a way to separate
 * the readline-dependent code from the rest of an application, which
 * can make it easier to mock by providing a subclass that overrides
 * readline-related methods.
 */
abstract class InteractiveApplication extends Application {


    use InteractiveTrait;


    public function __construct( array|Arguments|null $i_argv = null, ?LoggerInterface $log = null ) {
        # Require the readline extension.
        if ( ! extension_loaded( 'readline' ) ) {
            throw new LogicException( 'The readline extension is required.' );
        }
        parent::__construct( $i_argv, $log );
    }


}
