<?php


declare( strict_types = 1 );


use JDWX\App\Application;


class MyTestApplication extends Application {


    public ?int $niObservedExitStatus = null;

    public ?int $niErrorExitStatus = null;

    public ?int $niMainExitStatus = self::EXIT_SUCCESS;

    public bool|string|null $foo = null;

    /** @var ?callable */
    public $fnCallback = null;


    protected function exit( int $i_iStatus ) : void {
        $this->niObservedExitStatus = $i_iStatus;
    }


    protected function handleException( Exception $i_ex ) : ?int {
        parent::handleException( $i_ex );
        return $this->niErrorExitStatus;
    }


    /**
     * @noinspection PhpMethodNamingConventionInspection
     * @noinspection PhpUnused
     */
    protected function handleOption_foo( bool|string $i_bstArg ) : void {
        $this->foo = $i_bstArg;
    }


    protected function main() : int {
        if ( is_callable( $this->fnCallback ) ) {
            return ( $this->fnCallback )();
        }
        return $this->niMainExitStatus;
    }


}
