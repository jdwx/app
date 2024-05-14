<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\App;


use JDWX\Args\ArgumentParser;
use JDWX\Args\Arguments;
use JDWX\Args\BadArgumentException;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;


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
     * @param ?bool $i_nbDefault If null, the user must enter "yes" or "no".
     * @param bool $i_bReturnOnFail If readline() fails, return this value.
     * @return bool
     *
     * Ask a yes/no question.  Returns true for yes, false for no.  If the user
     * enters something that can't be interpreted as "yes" or "no", the question
     * is repeated. See ArgumentParser::parseBool() for a list of recognized
     * values.
     *
     * If the $i_nbDefault parameter is not null, the user can press Enter to
     * accept the default value.  It is your responsibility to ensure that the
     * prompt makes it clear what the default value is.
     *
     * The $i_bReturnOnFail parameter is used to handle the case where readline()
     * fails, usually because the user pressed Ctrl-D to signal end-of-file.
     */
    public function askYN( string $i_stPrompt, ?bool $i_nbDefault = null, bool $i_bReturnOnFail = false ) : bool {
        while ( true ) {
            $strYN = $this->readLine( $i_stPrompt );
            if ( false === $strYN ) {
                return $i_bReturnOnFail;
            }
            if ( '' === $strYN ) {
                if ( is_bool( $i_nbDefault ) ) {
                    return $i_nbDefault;
                }
                $this->warning( "Please enter 'yes' or 'no'." );
                continue;
            }
            try {
                return ArgumentParser::parseBool( $strYN );
            } catch ( BadArgumentException $e ) {
                $this->error( $e->getMessage() );
            }
        }
    }


    /**
     * Reads one line of input from the user.
     *
     * @param string $i_stPrompt The prompt to display.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readLine( string $i_stPrompt ) : false|string {
        return readline( $i_stPrompt );
    }


    /**
     *  Adds a line to the history list that readline maintains.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readlineAddHistory( string $i_stLine ) : bool {
        return readline_add_history( $i_stLine );
    }


    protected function readlineAddHistoryEx( string $i_stLine ) : void {
        $b = $this->readlineAddHistory( $i_stLine );
        if ( ! $b ) {
            throw new RuntimeException( "readline_add_history() failed" );
        }
    }


    /**
     * Sets the function readline will call when the user presses the tab key.
     *
     * @param callable $i_fnCompletion The completion function to install.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readlineCompletionFunction( callable $i_fnCompletion ) : bool {
        return readline_completion_function( $i_fnCompletion );
    }


    protected function readlineCompletionFunctionEx( callable $i_fnCompletion ) : void {
        $b = $this->readlineCompletionFunction( $i_fnCompletion );
        if ( ! $b ) {
            throw new RuntimeException( "readline_completion_function() failed" );
        }
    }


    /**
     * @return array The current readline info.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readlineInfo() : array {
        $rlInfo = readline_info();
        assert( is_array( $rlInfo ) );
        return $rlInfo;
    }


    /**
     * @param string $i_stName The name of the readline info to get or set.
     * @param mixed|null $i_xValue The value to set, if any.
     * @return mixed The previous value of the readline info.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readlineInfoSet( string $i_stName, mixed $i_xValue = null ) : mixed {
        if ( is_null( $i_xValue ) ) {
            return readline_info( $i_stName );
        }
        return readline_info( $i_stName, $i_xValue );
    }


    /**
     * Redraws the current input line. Useful if you printed output
     * during an autocomplete function and want to make sure the user
     * can still see what they're typing.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     */
    protected function readlineRedisplay() : void {
        readline_redisplay();
    }


}
