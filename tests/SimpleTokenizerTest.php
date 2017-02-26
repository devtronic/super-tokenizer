<?php
/**
 * This file is part of the Devtronic Super Tokenizer package.
 *
 * (c) Julian Finkler <julian@developer-heaven.de>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
/**
 * This file is part of the Devtronic Super Tokenizer package.
 *
 * (c) Julian Finkler <julian@developer-heaven.de>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Devtronic\Tests\SuperTokenizer;

use Devtronic\SuperTokenizer\SimpleTokenizer;
use Devtronic\SuperTokenizer\Tokenizer;
use Devtronic\SuperTokenizer\TokenizerInterface;
use PHPUnit\Framework\TestCase;

class SimpleTokenizerTest extends TestCase
{
    public function testConstruct()
    {
        $tokenizer = new SimpleTokenizer();

        $this->assertTrue($tokenizer instanceof Tokenizer);
        $this->assertTrue($tokenizer instanceof TokenizerInterface);
    }

    public function testGetSeparators()
    {
        $tokenizer = new SimpleTokenizer();
        $expected = [" ", "\t", "\n", "\r", "\0", "\x0B"];

        $this->assertSame($expected, $tokenizer->getSeparators());
    }

    public function testTokenizeSimple()
    {
        $tokenizer = new SimpleTokenizer();

        /** @noinspection SpellCheckingInspection */
        $input = "Hello my\tname\nis\rJulian";

        $expected = [
            [
                'type' => 1,
                'value' => 'Hello',
                'position' => 0,
            ],
            [
                'type' => 1,
                'value' => 'my',
                'position' => 6,
            ],
            [
                'type' => 1,
                'value' => 'name',
                'position' => 9,
            ],
            [
                'type' => 1,
                'value' => 'is',
                'position' => 14,
            ],
            [
                'type' => 1,
                'value' => 'Julian',
                'position' => 17,
            ],
        ];

        $this->assertSame($expected, $tokenizer->tokenize($input));
    }

    public function testTokenizeWithString()
    {
        $tokenizer = new SimpleTokenizer();

        $input = 'Hello my name is "Julian Finkler"';
        $expected = [
            [
                'type' => 1,
                'value' => 'Hello',
                'position' => 0,
            ],
            [
                'type' => 1,
                'value' => 'my',
                'position' => 6,
            ],
            [
                'type' => 1,
                'value' => 'name',
                'position' => 9,
            ],
            [
                'type' => 1,
                'value' => 'is',
                'position' => 14,
            ],
            [
                'type' => 10,
                'value' => '"Julian Finkler"',
                'position' => 17,
            ],
        ];
        $this->assertSame($expected, $tokenizer->tokenize($input));

        $input2 = 'Hello my name is \'Julian Finkler\'';
        $expected2 = [
            [
                'type' => 1,
                'value' => 'Hello',
                'position' => 0,
            ],
            [
                'type' => 1,
                'value' => 'my',
                'position' => 6,
            ],
            [
                'type' => 1,
                'value' => 'name',
                'position' => 9,
            ],
            [
                'type' => 1,
                'value' => 'is',
                'position' => 14,
            ],
            [
                'type' => 10,
                'value' => '\'Julian Finkler\'',
                'position' => 17,
            ],
        ];

        $this->assertSame($expected2, $tokenizer->tokenize($input2));
    }

    public function testTokenizeWithStringFailing()
    {
        $this->expectException(\ParseError::class);
        $this->expectExceptionMessage('Unexpected end of file. Expecting " near "Julian Finkler');

        $input = 'Hello my name is "Julian Finkler';
        $tokenizer = new SimpleTokenizer();
        $tokenizer->tokenize($input);
    }

    public function testTokenizeWithEscaping()
    {
        $tokenizer = new SimpleTokenizer();

        $input = 'Hello my\ name is "Julian\" Finkler"';
        $expected = [
            [
                'type' => 1,
                'value' => 'Hello',
                'position' => 0,
            ],
            [
                'type' => 1,
                'value' => 'my name',
                'position' => 6,
            ],
            [
                'type' => 1,
                'value' => 'is',
                'position' => 15,
            ],
            [
                'type' => 10,
                'value' => '"Julian" Finkler"',
                'position' => 18,
            ],
        ];

        $this->assertSame($expected, $tokenizer->tokenize($input));
    }

    public function testTokenizeWithBrackets()
    {
        $tokenizer = new SimpleTokenizer();

        $input = 'PHP (Hypertext Preprocessor)';

        $expected = [
            [
                'type' => 1,
                'value' => 'PHP',
                'position' => 0
            ],
            [
                'type' => 20,
                'value' => '(',
                'position' => 4
            ],
            [
                'type' => 1,
                'value' => 'Hypertext',
                'position' => 5
            ],
            [
                'type' => 1,
                'value' => 'Preprocessor',
                'position' => 15
            ],
            [
                'type' => 21,
                'value' => ')',
                'position' => 27
            ],
        ];

        $this->assertSame($expected, $tokenizer->tokenize($input));
    }

    public function testTokenizeWithMultipleBrackets()
    {
        $tokenizer = new SimpleTokenizer();

        /** @noinspection SpellCheckingInspection */
        $input = 'PHP (Hypertext Preprocessor [backronym]) is a "scripting language" {1234}';

        /** @noinspection SpellCheckingInspection */
        $expected = [
            [
                'type' => 1,
                'value' => 'PHP',
                'position' => 0,
            ],
            [
                'type' => 20,
                'value' => '(',
                'position' => 4,
            ],
            [
                'type' => 1,
                'value' => 'Hypertext',
                'position' => 5,
            ],
            [
                'type' => 1,
                'value' => 'Preprocessor',
                'position' => 15,
            ],
            [
                'type' => 20,
                'value' => '[',
                'position' => 28,
            ],
            [
                'type' => 1,
                'value' => 'backronym',
                'position' => 29,
            ],
            [
                'type' => 21,
                'value' => ']',
                'position' => 38,
            ],
            [
                'type' => 21,
                'value' => ')',
                'position' => 39,
            ],
            [
                'type' => 1,
                'value' => 'is',
                'position' => 41,
            ],
            [
                'type' => 1,
                'value' => 'a',
                'position' => 44,
            ],
            [
                'type' => 10,
                'value' => '"scripting language"',
                'position' => 46,
            ],
            [
                'type' => 20,
                'value' => '{',
                'position' => 67,
            ],
            [
                'type' => 1,
                'value' => '1234',
                'position' => 68,
            ],
            [
                'type' => 21,
                'value' => '}',
                'position' => 72,
            ],
        ];

        $this->assertSame($expected, $tokenizer->tokenize($input));
    }

    public function testTokenizeWithBracketsFailing()
    {
        $tokenizer = new SimpleTokenizer();

        $input = 'PHP (Hypertext Preprocessor] is a scripting language';

        $this->expectException(\ParseError::class);
        $this->expectExceptionMessage('Unexpected ], expecting )');
        $tokenizer->tokenize($input);
    }

    public function testTokenizeWithCustomTokens()
    {
        $tokenizer = new SimpleTokenizer();

        $customTokens = [
            40 => '=',
            45 => '@',
        ];

        $tokenizer->setCustomTokens($customTokens);

        $input = '@foobar = 22';

        $expected = [
            [
                'type' => 45,
                'value' => '@',
                'position' => 0,
            ],
            [
                'type' => 1,
                'value' => 'foobar',
                'position' => 1,
            ],
            [
                'type' => 40,
                'value' => '=',
                'position' => 8,
            ],
            [
                'type' => 1,
                'value' => '22',
                'position' => 10,
            ],
        ];

        $this->assertSame($expected, $tokenizer->tokenize($input));
    }

    public function testTokenizeWithLineEndings()
    {
        $tokenizer = new SimpleTokenizer();
        $tokenizer->addEOL = true;

        $input = "This\nis a test";

        $expected = [
            [
                'type' => 1,
                'value' => 'This',
                'position' => 0,
            ],
            [
                'type' => 25,
                'value' => "\n",
                'position' => 4,
            ],
            [
                'type' => 1,
                'value' => 'is',
                'position' => 5,
            ],
            [
                'type' => 1,
                'value' => 'a',
                'position' => 8,
            ],
            [
                'type' => 1,
                'value' => 'test',
                'position' => 10,
            ]
        ];

        $this->assertSame($expected, $tokenizer->tokenize($input));
    }
}