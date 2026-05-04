<?php


declare( strict_types = 1 );


use JDWX\App\Term;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Term::class )]
final class TermTest extends TestCase {


    public function testBold() : void {
        self::assertSame( "\033[1m", Term::bold() );
    }


    public function testColor() : void {
        self::assertSame( "\033[32m", Term::color( Term::GREEN ) );
        self::assertSame( "\033[42m", Term::color( i_stBG: Term::GREEN ) );
        self::assertSame( "\033[30;41m", Term::color( Term::BLACK, Term::RED ) );
    }


    public function testReadline() : void {
        $markers = $this->inferReadlineMarkers();
        $stRed = Term::color( Term::RED );
        $stReadlineRed = $markers[ 0 ] . $stRed . $markers[ 1 ];
        self::assertSame( $stReadlineRed, Term::readline( $stRed ) );

        $stBold = Term::bold();
        $stColor = Term::color( Term::GREEN );
        $stReset = Term::reset();

        $stPrompt = "{$stBold}{$stColor}prompt{$stReset} ";
        $stEscapedPrompt = "{$markers[ 0 ]}{$stBold}{$stColor}{$markers[ 1 ]}prompt{$markers[0]}{$stReset}{$markers[1]} ";

        self::assertSame( $stEscapedPrompt, Term::readline( $stPrompt ) );
    }


    public function testReadlineTwice() : void {
        $markers = $this->inferReadlineMarkers();
        $stRed = Term::color( Term::RED );
        $stReadlineRed = $markers[ 0 ] . $stRed . $markers[ 1 ];
        self::assertSame( $stReadlineRed, Term::readline( $stRed ) );
        self::assertSame( $stReadlineRed, Term::readline( Term::readline( $stRed ) ) );
    }


    public function testReset() : void {
        self::assertSame( "\033[0m", Term::reset() );
    }


    /**
     * @return list<string> The inferred readline markers for the current readline backend.
     */
    private function inferReadlineMarkers() : array {
        $term = new class extends Term {


            /** @return list<string> */
            public function readlineMarkersRelay() : array {
                return static::readlineMarkers();
            }


        };
        return $term->readlineMarkersRelay();
    }


}
