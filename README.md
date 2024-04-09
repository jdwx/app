# jdwx/app

A trivial PHP framework for command line apps.

This is primarily useful when you have a bunch of discrete command-line 
applications in a PHP codebase.  It keeps things out of the global namespace,
especially functions called "main," without a lot of boilerplate code.

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/app
```

Or download the source from GitHub: https://github.com/jdwx/app.git

## Requirements

This framework requires PHP 8.2 or later. It might work with earlier versions 
of PHP 8, but it has not been tested with them.


## Usage

To use this framework, create a class that extends the Application class and 
implements a protected main() method. The main() method should return an 
integer, which will be the exit status of the process.  If you do not want the
application to terminate the process after the main() function returns, you can 
override the exit() method.

The Application class takes advantage of the 
[jdwx/args](https://github.com/jdwx/args) library to provide robust 
type-safe handling of command-line arguments.  It also supports the PSR 
LoggerInterface standard.

```php

require 'vendor/autoload.php';


(new class( $argv ) extends \JDWX\App\Application {

    protected function main() : int {
        echo "Hello, world!\n";
        if ( $this->args->empty() ) {
            echo "You didn't enter anything.\n";
        } else {
            echo "You entered: ", $this->args->endWithString(), "\n";
        }
        return 0;
    }

})->run();

```

## Stability

This framework is considered stable and is used in production code.  However,
because it is used primarily for interactive tasks, it is difficult to fully 
test.  It is recommended that you test your applications thoroughly.

## History

This framework was refactored out of a larger codebase and first released 
as part of the [jdwx/cli](https://github.com/jdwx/cli) framework before
quickly being separated into its own standalone module in 2024 for the
benefit of command-line applications that are *not* REPL-style interactive
tools. 
