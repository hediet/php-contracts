PHP Contracts - A Powerfull Assertion Library for PHP
=====================================================

PHP Contracts is an assertion library for PHP to validate arguments and invariants.
This library aims to replace my library [nunzion/php-expect](https://bitbucket.org/nunzion/php-expect) in the long term.

Warning
-------
This library is still in development!
However, the signature of the method `Contract::requires` will not change 
and it is guaranteed that an exception is beeing thrown if the condition evaluates to `false`.

Installation
------------
You can use [Composer](http://getcomposer.org/) to download and install PHP Contracts.
To add PHP Contracts to your project, simply add a dependency on hediet/contracts to your project's `composer.json` file.

Here is a minimal example of a `composer.json` file that just defines a dependency on PHP Contracts:

``` json
{
    "require": {
        "hediet/contracts": "dev-master"
    }
}
```

Usage
-----

Using this library is as simple as calling `Contract::requires` with an arbitrary condition:

``` php

use Hediet\Contract;

function sum($a, $b)
{
    Contract::requires(is_int($a) && is_int($b));
}

```

If the specified condition evaluates to `false`, PHP Contracts analyzes the condition and throws
an exception with an appropriate error message.

The following code will throw an `\InvalidArgumentException` with the message `Argument 'b' must be of type 'integer', but is of type 'string'.`:
``` php
sum(1, "test");
```

### Explicit Supported Tests

Currently PHP Contracts understands the following conditions:
* All is_TYPE functions for TYPE being a primitive.
* Comparison operators like `<`, `<=`, `>`, `>=`.
* Disjunction of type constraints, e.g. `is_int($a) || is_float($a) || $a === null`. 
  Internally, a type constraint is being created for $a which denotes that $a must be of type `int|float|null`.
* Conjunction of constraints.

However, currently all expressions must be constants or arguments.

Internals
---------
If the condition evaluates to `true`, the requires method returns immediately. 
Thus, if a condition does not fail, there is no significant performance decrease.

If a condition fails, i.e. evaluates to `false`, the stacktrace is used to determine the location of
the `requires` call. After that, the invocation is parsed with nikic/PHP-Parser and converted
to a set of constraints. These constraints may reference expressions to which the constraints are applied to.
By using the stacktrace a second time, the arguments and the value of `$this` can be obtained for the context
of the invocation to evaluate these expressions, so that a constraint can add an explanation of why he has failed.

Todos
------
* Support deep expressions, so that conditions like `Contract::requires(count($a) > 0)` can be analyzed.
  Since $a may be obtained from the stacktrace and count is a pure method, `count($a)` can be evaluated without side effects.
* Support various array tests.
* Throw an invariant exception if no argument is referenced in the condition.
* Use optionally provided values to evaluate expressions which uses variables that are neither arguments nor `$this`.
* Add a reflection API which parses the first `Contract::requires` calls and returns their corresponding constraints.

Author
------
Henning Dieterichs - henning.dieterichs@hediet.de

License
-------
PHP Expect is licensed under the MIT License.