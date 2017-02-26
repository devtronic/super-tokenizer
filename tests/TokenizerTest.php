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

use Devtronic\SuperTokenizer\Tokenizer;
use Devtronic\SuperTokenizer\TokenizerInterface;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
    public function testConstruct()
    {
        $tokenizer = new Tokenizer();

        $this->assertTrue($tokenizer instanceof TokenizerInterface);
    }


    public function testGetTokenName()
    {
        $tokenizer = new Tokenizer();

        $this->assertSame('TT_TOKEN', $tokenizer->getTokenName(1));
    }

    public function testPreTokenize()
    {
        $tokenizer = new Tokenizer();

        /** @noinspection SpellCheckingInspection */
        $source = "hello\r\nworld\rsimple\ntest";
        /** @noinspection SpellCheckingInspection */
        $expected = "hello\nworld\nsimple\ntest";

        $this->assertSame($expected, $tokenizer->preTokenize($source));
    }

    public function testPostTokenize()
    {
        $tokenizer = new Tokenizer();

        $result = ['hello', 'world'];
        $expected = $result;

        $this->assertSame($expected, $tokenizer->postTokenize($result));
    }

    public function testGetSeparators()
    {
        $tokenizer = new Tokenizer();

        $separators = [' '];

        $this->assertSame($separators, $tokenizer->getSeparators());
    }

    public function testHandlePosition()
    {
        $tokenizer = new Tokenizer();

        $this->assertTrue($tokenizer->handlePosition('x'));
        $this->assertFalse($tokenizer->handlePosition(' '));
    }

    public function testTokenizeSimple()
    {
        $tokenizer = new Tokenizer();

        $input = 'Hello my name is Julian';
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
}