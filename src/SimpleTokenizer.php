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
 * A simple tokenizer
 *
 * @package Devtronic\SuperTokenizer
 */
class SimpleTokenizer extends Tokenizer
{
    /** Type Constants **/
    const TT_STRING = 10;
    const TT_BRACKET_OPEN = 20;
    const TT_BRACKET_CLOSE = 21;

    /** @var array Custom Tokens (Key = Class Constant, Value = Token-Character) */
    protected $customTokens = [];

    /** @var array token separators */
    protected $separators = [" ", "\t", "\n", "\r", "\0", "\x0B"];

    /** @var array string enclosure characters */
    protected $enclosures = ['"', '\''];
    /** @var null|string The current used enclosure character */
    protected $usedEnclosure = null;

    /** @var array Brackets (Key = Opening, Value = Closing) */
    protected $brackets = ['{' => '}', '(' => ')', '[' => ']'];
    protected $bracketsFlipped;

    /** @var int Number of escaped characters (used for calculating the position in source) */
    private $escaped = 0;

    /** @var string The Bracket-History (used for validating correct opening and closing) */
    private $bracketHistory = '';

    public function __construct()
    {
        $this->bracketsFlipped = array_flip($this->brackets);
    }

    /** {@inheritdoc} */
    public function tokenize(string $source): array
    {
        $result = parent::tokenize($source);

        if ($this->usedEnclosure !== null) {
            $lastToken = end($this->tokens);
            throw new \ParseError(
                sprintf('Unexpected end of file. Expecting %s near %s', $this->usedEnclosure, $lastToken['value'])
            );
        }

        return $result;
    }

    /** {@inheritdoc} */
    public function handlePosition(string $char): bool
    {

        if ($this->index > 0 && $this->input[$this->index - 1] == '\\') {
            $this->currentToken .= $char;
            return false;
        }
        if ($char == '\\') {
            $this->escaped++;
            return false;
        }

        if (in_array($char, $this->separators) && $this->usedEnclosure === null) {
            $position = $this->index - strlen($this->currentToken);
            $this->currentToken = $this->addToken(self::TT_TOKEN, $this->currentToken, $position);
            return false;
        }

        if ($this->usedEnclosure === null) {
            foreach ($this->customTokens as $type => $customToken) {
                if ($char == $customToken) {
                    if (trim($this->currentToken) != '') {
                        $position = $this->index - strlen($this->currentToken);
                        $this->currentToken = $this->addToken(self::TT_TOKEN, $this->currentToken, $position);
                    }
                    $this->currentToken = $this->addToken($type, $char, $this->index);
                    return false;
                }
            }
        }

        $this->currentToken .= $char;

        if (in_array($char, $this->enclosures)) {

            if ($this->usedEnclosure === null) {
                $this->usedEnclosure = $char;
            } elseif ($this->usedEnclosure === $char) {
                $this->usedEnclosure = null;
            }

            if ($this->usedEnclosure === null) {
                $position = $this->index - strlen($this->currentToken);
                $this->currentToken = $this->addToken(self::TT_STRING, $this->currentToken, $position + 1);
                $this->usedEnclosure = null;
                return false;
            }
        }

        if ($this->usedEnclosure === null) {

            if (array_key_exists($char, $this->brackets)) {
                $token = substr($this->currentToken, 0, -1);
                if (trim($token) != '') {
                    $position = $this->index - strlen($token);
                    $this->currentToken = $this->addToken(self::TT_TOKEN, $token, $position);
                }
                $this->currentToken = $this->addToken(self::TT_BRACKET_OPEN, $char, $this->index);
                $this->bracketHistory .= $char;
                return false;
            } elseif (array_key_exists($char, $this->bracketsFlipped) && substr($this->bracketHistory, -1) == $this->bracketsFlipped[$char]) {
                $token = substr($this->currentToken, 0, -1);
                if (trim($token) != '') {
                    $position = $this->index - strlen($token);
                    $this->currentToken = $this->addToken(self::TT_TOKEN, $token, $position);
                }
                $this->currentToken = $this->addToken(self::TT_BRACKET_CLOSE, $char, $this->index);
                $this->bracketHistory = substr($this->bracketHistory, 0, -1);
                return true;
            } elseif (array_key_exists($char, $this->bracketsFlipped)) {
                throw new \ParseError(sprintf("Unexpected %s, expecting %s", $char, $this->brackets[substr($this->bracketHistory, -1)]));
            }
        }

        return true;
    }

    /** {@inheritdoc} */
    public function addToken(int $type, string $value, $position = null)
    {
        $position -= $this->escaped;
        $this->escaped = 0;
        return parent::addToken($type, $value, $position);
    }

    /**
     * Sets the custom tokens
     * @param array $customTokens The custom tokens
     */
    public function setCustomTokens(array $customTokens)
    {
        $this->customTokens = $customTokens;
    }

}