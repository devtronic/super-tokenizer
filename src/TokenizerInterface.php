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

namespace Devtronic\SuperTokenizer;

/**
 * Interface for tokenizer
 *
 * @package Devtronic\SuperTokenizer
 */
interface TokenizerInterface
{
    /**
     * This method is executed before tokenize
     * For example to normalize linen endings etc.
     *
     * @param string $source The Source passed to tokenize
     * @return string The modified source
     */
    public function preTokenize(string $source): string;

    /**
     * This method tokenize the input string
     *
     * @param string $source The Source
     * @return array The Tokens
     */
    public function tokenize(string $source): array;

    /**
     * This method is executed after the tokenize task is done
     * You can use this method for number detection etc.
     *
     * @param array $result The Tokens from tokenize
     * @return array The Modified Tokens
     */
    public function postTokenize(array $result): array;

    /**
     * This is the heart of the tokenizer.
     * Every char of the source is passed to this method for processing
     *
     * @param string $char The current char
     * @return bool Actually not used. In future: True = Do nothing, False = Calls Continue the for-loop
     */
    public function handlePosition(string $char): bool;

    /**
     * This Method adds a token to the token array
     *
     * @param int $type The token type (class constant starts with TT_ (short for TokenType_))
     * @param string $value The content of the Token
     * @param null|int $position The Position in the Source
     * @return mixed
     */
    public function addToken(int $type, string $value, $position = null);

    /**
     * Returns the separators of the tokenizer
     *
     * @return array The separators
     */
    public function getSeparators(): array;

    /**
     * Transforms the token type into the name of the class constant
     *
     * @param int $tokenType The token type
     * @return string The name
     */
    public function getTokenName(int $tokenType): string;
}