[![GitHub tag](https://img.shields.io/packagist/v/devtronic/super-tokenizer.svg)](https://github.com/Devtronic/super-tokenizer)
[![Packagist](https://img.shields.io/packagist/l/Devtronic/super-tokenizer.svg)](https://github.com/Devtronic/super-tokenizer/blob/master/LICENSE)
[![Travis](https://img.shields.io/travis/Devtronic/super-tokenizer.svg)](https://travis-ci.org/Devtronic/super-tokenizer/)
[![Packagist](https://img.shields.io/packagist/dt/Devtronic/super-tokenizer.svg)](https://github.com/Devtronic/super-tokenizer)

# Super Tokenizer

Super Tokenizer is a ultra dynamic and easy to use tokenizer written in PHP

### Installation
```bash
composer require devtronic/super-tokenizer
```

### Usage
#### Minimal Tokenizer
```php
<?php

use Devtronic\SuperTokenizer\Tokenizer;

require_once __DIR__ . '/vendor/autoload.php';

$tokenizer = new Tokenizer();

$sample = 'Minimal tokenizer example';

$tokens = $tokenizer->tokenize($sample);
print_r($tokens);
```

Prints
```
Array
(
    [0] => Array
        (
            [type] => 1
            [value] => Minimal
            [position] => 0
        )

    [1] => Array
        (
            [type] => 1
            [value] => tokenizer
            [position] => 8
        )

    [2] => Array
        (
            [type] => 1
            [value] => example
            [position] => 18
        )
)
```


You can also get the name of the token with the getTokenName()-Method
```php
<?php
// ...
foreach ($tokens as &$token) {
    $token['name'] = $tokenizer->getTokenName($token['type']);
}

print_r($tokens);
```

Prints
```
Array
(
    [0] => Array
        (
            [type] => 1
            [value] => Minimal
            [position] => 0
            [name] => TT_TOKEN
        )

    [1] => Array
        (
            [type] => 1
            [value] => tokenizer
            [position] => 8
            [name] => TT_TOKEN
        )

    [2] => Array
        (
            [type] => 1
            [value] => example
            [position] => 18
            [name] => TT_TOKEN
        )
)
```
#### Simple Tokenizer

The simple tokenizer also allows to use strings ("hello" or 'hello'), Brackets ('()', '[]' and '{}'), multiple separators
(" ", "\t", "\n", "\r", "\0", "\x0B") and character escaping with a backslash (\)

```php
<?php

use Devtronic\SuperTokenizer\SimpleTokenizer;

require_once __DIR__ . '/vendor/autoload.php';

$tokenizer = new SimpleTokenizer();

$sample = '"Simple" \'Tokenizer\' with\ different brackets [a, b] (c,d), {0, 1}';

$tokens = $tokenizer->tokenize($sample);

foreach ($tokens as &$token) {
    $token['name'] = $tokenizer->getTokenName($token['type']);
}

print_r($tokens);
```

Prints
```
Array
(
    [0] => Array
        (
            [type] => 10
            [value] => "Simple"
            [position] => 0
            [name] => TT_STRING
        )

    [1] => Array
        (
            [type] => 10
            [value] => 'Tokenizer'
            [position] => 9
            [name] => TT_STRING
        )

    [2] => Array
        (
            [type] => 1
            [value] => with different
            [position] => 21
            [name] => TT_TOKEN
        )

    [3] => Array
        (
            [type] => 1
            [value] => brackets
            [position] => 37
            [name] => TT_TOKEN
        )

    [4] => Array
        (
            [type] => 20
            [value] => [
            [position] => 46
            [name] => TT_BRACKET_OPEN
        )

    [5] => Array
        (
            [type] => 1
            [value] => a,
            [position] => 47
            [name] => TT_TOKEN
        )

    [6] => Array
        (
            [type] => 1
            [value] => b
            [position] => 50
            [name] => TT_TOKEN
        )

    [7] => Array
        (
            [type] => 21
            [value] => ]
            [position] => 51
            [name] => TT_BRACKET_CLOSE
        )

    [8] => Array
        (
            [type] => 20
            [value] => (
            [position] => 53
            [name] => TT_BRACKET_OPEN
        )

    [9] => Array
        (
            [type] => 1
            [value] => c,d
            [position] => 54
            [name] => TT_TOKEN
        )

    [10] => Array
        (
            [type] => 21
            [value] => )
            [position] => 57
            [name] => TT_BRACKET_CLOSE
        )

    [11] => Array
        (
            [type] => 1
            [value] => ,
            [position] => 58
            [name] => TT_TOKEN
        )

    [12] => Array
        (
            [type] => 20
            [value] => {
            [position] => 60
            [name] => TT_BRACKET_OPEN
        )

    [13] => Array
        (
            [type] => 1
            [value] => 0,
            [position] => 61
            [name] => TT_TOKEN
        )

    [14] => Array
        (
            [type] => 1
            [value] => 1
            [position] => 64
            [name] => TT_TOKEN
        )

    [15] => Array
        (
            [type] => 21
            [value] => }
            [position] => 65
            [name] => TT_BRACKET_CLOSE
        )
)
```

#### Custom tokens / Custom tokenizer
To add your own tokens, you can simply create a custom tokenizer class like this:
```php
<?php

use Devtronic\SuperTokenizer\SimpleTokenizer;

require_once __DIR__ . '/vendor/autoload.php';

class CustomTokenizer extends SimpleTokenizer
{
    const TT_DOLLAR = 30;
    const TT_EQUALS = 35;

    public function __construct()
    {
        parent::__construct();

        $this->customTokens = [
            self::TT_DOLLAR => '$',
            self::TT_EQUALS => '='
        ];
    }
}

$tokenizer = new CustomTokenizer();

$sample = '$var = 1234';
$tokens = $tokenizer->tokenize($sample);

foreach ($tokens as &$token) {
    $token['name'] = $tokenizer->getTokenName($token['type']);
}

print_r($tokens);
```

Prints
```
Array
(
    [0] => Array
        (
            [type] => 30
            [value] => $
            [position] => 0
            [name] => TT_DOLLAR
        )

    [1] => Array
        (
            [type] => 1
            [value] => var
            [position] => 1
            [name] => TT_TOKEN
        )

    [2] => Array
        (
            [type] => 35
            [value] => =
            [position] => 5
            [name] => TT_EQUALS
        )

    [3] => Array
        (
            [type] => 1
            [value] => 1234
            [position] => 7
            [name] => TT_TOKEN
        )
)
```

The preTokenize()-Method allows you to modify the input source before tokenizing (normalize linendings...).
With postTokenize() you can modify the result of the tokenize method (detect numbers, ...)

### Testing
```
phpunit
```

### Contributing
- Fork the repository
- Create a pull request