<?php
namespace Plitz\Compilers;

/**
 * Simple, chainable API allowing
 */
class CodeEmitter
{
    /**
     * Output stream
     * @var resource
     */
    protected $stream;

    /**
     * Indentation level
     * @var int
     */
    protected $indentationLevel = 0;

    /**
     * @var string
     */
    protected $indentationCharacter;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
        $this->indentationCharacter = "\t";
    }

    public function setIdentationCharacter($char)
    {
        $this->indentationCharacter = $char;
    }

    public function indent()
    {
        $this->indentationLevel++;
        return $this;
    }

    public function outdent()
    {
        $this->indentationLevel--;
        return $this;
    }

    public function raw($text)
    {
        fwrite($this->stream, $text);
        return $this;
    }

    public function line($text)
    {
        $this->raw(str_repeat($this->indentationCharacter, $this->indentationLevel) . $text . PHP_EOL);
        return $this;
    }

    public function lineNoEnding($text)
    {
        $this->raw(str_repeat($this->indentationCharacter, $this->indentationLevel) . $text);
        return $this;
    }

    public function newline()
    {
        $this->raw(PHP_EOL);
        return $this;
    }
}
