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

class Tokenizer implements TokenizerInterface
{
    /** Type Constants **/
    const TT_TOKEN = 1;

    /** @var array token separators */
    protected $separators = [' '];

    /** @var array The tokens from tokenize() */
    protected $tokens = [];

    /** @var string The current token */
    protected $currentToken = '';

    /** @var int The position in the source */
    protected $index = 0;

    /** @var string|null Each character of the source */
    protected $input = null;

    /** {@inheritdoc} */
    public function preTokenize(string $source): string
    {
        $source = str_replace("\r\n", '<CRLF>', $source);
        $source = str_replace("\r", '<CR>', $source);
        $source = str_replace("\n", '<LF>', $source);

        $source = str_replace(['<CRLF>', '<CR>', '<LF>'], "\n", $source);

        return $source;
    }

    /** {@inheritdoc} */
    public function tokenize(string $source): array
    {
        $this->tokens = [];

        $source = $this->preTokenize($source);

        $chars = str_split($source);
        $this->input = $chars;

        foreach ($chars as $index => $char) {
            $this->index = $index;
            $result = $this->handlePosition($char);

            if ($result === false) {
                continue;
            }
        }
        $position = $this->index - strlen($this->currentToken) + 1;
        $this->currentToken = $this->addToken(self::TT_TOKEN, $this->currentToken, $position);

        $this->tokens = $this->postTokenize($this->tokens);
        $this->input = null;

        return $this->tokens;
    }

    /** {@inheritdoc} */
    public function handlePosition(string $char): bool
    {
        if (in_array($char, $this->separators)) {
            $position = $this->index - strlen($this->currentToken);
            $this->currentToken = $this->addToken(self::TT_TOKEN, $this->currentToken, $position);
            return false;
        }

        $this->currentToken .= $char;

        return true;
    }

    /** {@inheritdoc} */
    public function addToken(int $type, string $value, $position = null)
    {
        if (trim($value) != '') {
            $this->tokens[] = [
                'type' => $type,
                'value' => $value,
                'position' => $position,
            ];
        }
        return '';
    }

    /** {@inheritdoc} */
    public function postTokenize(array $result): array
    {
        return $result;
    }

    /** {@inheritdoc} */
    public function getTokenName(int $tokenType): string
    {
        $tokenName = '';

        $reflection = new \ReflectionClass(get_class($this));
        $constants = $reflection->getConstants();
        foreach ($constants as $name => $type) {
            if ($tokenType === $type) {
                $tokenName = $name;
                break;
            }
        }

        return $tokenName;
    }

    /** {@inheritdoc} */
    public function getSeparators(): array
    {
        return $this->separators;
    }

}