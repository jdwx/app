<?php /** @noinspection PhpLackOfCohesionInspection */


/** @noinspection PhpConstantNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\App;


/**
 * Right now this only supports XTERM/VT100/ANSI terminal escape sequences.
 *
 * Sadly the ncurses extension is no longer available on all the platforms
 * we need to support. So to the extent this looks like reinventing the
 * wheel (poorly), that's because it is.
 */
class Term {


    public const DEFAULT        = 'default';

    public const BLACK          = 'black';

    public const RED            = 'red';

    public const GREEN          = 'green';

    public const YELLOW         = 'yellow';

    public const BLUE           = 'blue';

    public const MAGENTA        = 'magenta';

    public const CYAN           = 'cyan';

    public const WHITE          = 'white';

    public const BRIGHT_BLACK   = 'bright_black';

    public const BRIGHT_RED     = 'bright_red';

    public const BRIGHT_GREEN   = 'bright_green';

    public const BRIGHT_YELLOW  = 'bright_yellow';

    public const BRIGHT_BLUE    = 'bright_blue';

    public const BRIGHT_MAGENTA = 'bright_magenta';

    public const BRIGHT_CYAN    = 'bright_cyan';

    public const BRIGHT_WHITE   = 'bright_white';


    /** Warning: If you use this, you are a bad person. */
    public static function blink() : string {
        return "\033[5m";
    }


    public static function bold() : string {
        return "\033[1m";
    }


    public static function clear() : string {
        return "\033[2J";
    }


    public static function color( ?string $i_stFG = null, ?string $i_stBG = null ) : string {
        if ( $i_stFG ) {
            if ( $i_stBG ) {
                return "\033[" . self::colorFG( $i_stFG ) . ';' . self::colorBG( $i_stBG ) . 'm';
            }

            return "\033[" . self::colorFG( $i_stFG ) . 'm';
        }

        if ( $i_stBG ) {
            return "\033[" . self::colorBG( $i_stBG ) . 'm';
        }

        return "\033[0m";
    }


    public static function column( int $i_uColumn ) : string {
        return "\033[{$i_uColumn}G";
    }


    public static function down( int $i_uLines = 1 ) : string {
        return "\033[{$i_uLines}B";
    }


    public static function home() : string {
        return "\033[H";
    }


    public static function italic() : string {
        return "\033[3m";
    }


    public static function left( int $i_uColumns = 1 ) : string {
        return "\033[{$i_uColumns}D";
    }


    public static function pos( int $i_uRow, int $i_uColumn ) : string {
        return "\033[{$i_uRow};{$i_uColumn}H";
    }


    /** FreeBSD wants escape sequences, but macOS does not. Not sure about Linux yet. */
    public static function readline( string $i_stControl ) : string {
        if ( posix_uname()[ 'sysname' ] === 'FreeBSD' ) {
            return chr( 1 ) . $i_stControl . chr( 2 );
        }
        return $i_stControl;
    }


    public static function reset() : string {
        return "\033[0m";
    }


    public static function restore() : string {
        return match ( $_ENV[ 'TERM' ] ) {
            'xterm', 'xterm-256color' => "\0338",
            default => "\033[u",
        };
    }


    public static function reverse() : string {
        return "\033[7m";
    }


    public static function right( int $i_uColumns = 1 ) : string {
        return "\033[{$i_uColumns}C";
    }


    public static function save() : string {
        return match ( $_ENV[ 'TERM' ] ) {
            'xterm', 'xterm-256color' => "\0337",
            default => "\033[s",
        };
    }


    /** Warning: This is not widely supported. */
    public static function strike() : string {
        return "\033[9m";
    }


    public static function textBackground( string $i_stText, string $i_stColor ) : string {
        return self::color( i_stBG: $i_stColor ) . $i_stText . self::color( i_stBG: self::DEFAULT );
    }


    public static function textBlink( string $i_stText ) : string {
        return self::blink() . $i_stText . self::reset();
    }


    public static function textBold( string $i_stText ) : string {
        return self::bold() . $i_stText . self::reset();
    }


    public static function textColor( string $i_stText, string $i_stFG, string $i_stBG ) : string {
        return self::color( $i_stFG, $i_stBG ) . $i_stText . self::color( self::DEFAULT, self::DEFAULT );
    }


    public static function textForeground( string $i_stText, string $i_stColor ) : string {
        return "\033[" . self::color( $i_stColor ) . $i_stText . self::color( self::DEFAULT );
    }


    public static function textItalic( string $i_stText ) : string {
        return self::italic() . $i_stText . self::reset();
    }


    /** Warning: This is not widely supported. */
    public static function textStrike( string $i_stText ) : string {
        return self::strike() . $i_stText . self::reset();
    }


    public static function textUnderline( string $i_stText ) : string {
        return self::underline() . $i_stText . self::reset();
    }


    public static function underline() : string {
        return "\033[4m";
    }


    public static function up( int $i_uLines = 1 ) : string {
        return "\033[{$i_uLines}A";
    }


    private static function colorBG( string $i_stColor ) : string {
        return match ( $i_stColor ) {
            self::BLACK => '40',
            self::RED => '41',
            self::GREEN => '42',
            self::YELLOW => '43',
            self::BLUE => '44',
            self::MAGENTA => '45',
            self::CYAN => '46',
            self::WHITE => '47',
            self::DEFAULT => '49',
            self::BRIGHT_BLACK => '100',
            self::BRIGHT_RED => '101',
            self::BRIGHT_GREEN => '102',
            self::BRIGHT_YELLOW => '103',
            self::BRIGHT_BLUE => '104',
            self::BRIGHT_MAGENTA => '105',
            self::BRIGHT_CYAN => '106',
            self::BRIGHT_WHITE => '107',
            default => throw new \InvalidArgumentException( "Unknown color: {$i_stColor}" ),
        };
    }


    /** @return string ANSI terminal color code */
    private static function colorFG( string $i_stColor ) : string {
        return match ( $i_stColor ) {
            self::BLACK => '30',
            self::RED => '31',
            self::GREEN => '32',
            self::YELLOW => '33',
            self::BLUE => '34',
            self::MAGENTA => '35',
            self::CYAN => '36',
            self::WHITE => '37',
            self::DEFAULT => '39',
            self::BRIGHT_BLACK => '90',
            self::BRIGHT_RED => '91',
            self::BRIGHT_GREEN => '92',
            self::BRIGHT_YELLOW => '93',
            self::BRIGHT_BLUE => '94',
            self::BRIGHT_MAGENTA => '95',
            self::BRIGHT_CYAN => '96',
            self::BRIGHT_WHITE => '97',
            default => throw new \InvalidArgumentException( "Unknown color: {$i_stColor}" ),
        };
    }


}
