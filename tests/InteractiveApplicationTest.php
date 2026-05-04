<?php


declare( strict_types = 1 );


use JDWX\App\InteractiveApplication;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( InteractiveApplication::class )]
final class InteractiveApplicationTest extends TestCase {


    public function testDefaultPrompt() : void {
        $app = $this->makeDummyApp();
        self::assertSame( '> ', $app->getDefaultPrompt() );
        $app->setDefaultPrompt( '$ ' );
        self::assertSame( '$ ', $app->getDefaultPrompt() );
    }


    public function testPrompt() : void {
        $app = $this->makeDummyApp();
        self::assertSame( '> ', $app->getPrompt() );
        self::assertSame( '$ ', $app->getPrompt( '$ ' ) );
        $app->setDefaultPrompt( '# ' );
        self::assertSame( '# ', $app->getPrompt() );
        self::assertSame( '$ ', $app->getPrompt( '$ ' ) );
    }


    private function makeDummyApp() : InteractiveApplication {
        return new class extends InteractiveApplication {


            protected function main() : int {
                return 0;
            }


        };
    }


}
