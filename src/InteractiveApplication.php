<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\App;


use JDWX\Args\ArgumentParser;
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


    public function __construct( array|Arguments|null $i_argv = null, ?LoggerInterface $log = null ) {
        # Require the readline extension.
        if ( ! extension_loaded( 'readline' ) ) {
            throw new LogicException( "The readline extension is required." );
        }
        parent::__construct( $i_argv, $log );
    }


    /**
     * @param string $i_stPrompt
     * @return bool
     *
     * Ask a yes/no question.  Returns true for yes, false for no.  If the user
     * enters something that can't be interpreted as "yes" or "no", the question
     * is repeated. See ArgumentParser::parseBool() for a list of recognized
     * values.
     */
    public function askYN( string $i_stPrompt ) : bool {
        while ( true ) {
            $strYN = $this->readLine( $i_stPrompt );
            $bYN = ArgumentParser::parseBool( $strYN );
            if ( $bYN === true || $bYN === false ) return $bYN;
        }
    }


    /**
     * Reads one line of input from the user.
     *
     * @param string $i_stPrompt The prompt to display.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readLine( string $i_stPrompt ) : bool|string {
        return readline( $i_stPrompt );
    }


    /**
     * @return array The current readline info.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readlineInfo() : array {
        # This prevents readline from ever looking at filenames as an autocomplete option.
        readline_info( 'attempted_completion_over', 1 );
        $rlInfo = readline_info();
        assert( is_array( $rlInfo ) );
        return $rlInfo;
    }


}
