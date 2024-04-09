<?php


declare( strict_types = 1 );


use JDWX\App\Application;


class MyTestApplication extends Application {


    public ?int $iExitStatus = null;
    public bool|string|null $foo = null;
    public ?Exception $ex = null;


    protected function exit( int $i_iStatus ) : void {
        $this->iExitStatus = $i_iStatus;
    }


    protected function handleException( Exception $i_ex ) : ?int {
        $this->ex = $i_ex;
        return null;
    }


    /**
     * @noinspection PhpMethodNamingConventionInspection
     * @noinspection PhpUnused
     */
    protected function handleOption_foo( bool|string $i_bstArg ) : void {
        $this->foo = $i_bstArg;
    }



    protected function main() : int {
        return 0;
    }


}
