<?php
namespace Plitz\Tests\Compilers;

use Plitz\Compilers\JsCompiler;
use Plitz\Compilers\PhpCompiler;
use Plitz\Lexer\Lexer;
use Plitz\Parser\Parser;
use Symfony\Component\Yaml\Yaml;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var resource
     */
    private $inputStream;

    /**
     * @var resource
     */
    private $outputStream;

    /**
     * @var Lexer
     */
    private $lexer;

    protected function setUp()
    {
        $this->inputStream = fopen("php://temp", "r+");
        $this->outputStream = fopen("php://temp", "r+");
        $this->lexer = new Lexer($this->inputStream);
    }

    protected function tearDown()
    {
        fclose($this->inputStream);
        fclose($this->outputStream);
    }

    /**
     * @dataProvider provideYamlCases
     * @param string $language
     * @param string $template
     * @param string $expectedOutput
     */
    public function testYamlCases($language, $template, $expectedOutput)
    {
        // write template to input stream
        fwrite($this->inputStream, $template);
        fseek($this->inputStream, 0, SEEK_SET);

        switch ($language) {
            case 'JS':
                $compiler = new JsCompiler($this->outputStream, "    ");
                break;
            case 'PHP':
                $compiler = new PhpCompiler($this->outputStream);
                break;
            default:
                throw new \InvalidArgumentException('Invalid language "' . $language . '" given');
        }

        $parser = new Parser($this->lexer->lex(), $compiler);
        $parser->parse();

        // rewind output stream
        fseek($this->outputStream, 0, SEEK_SET);

        $this->assertEquals($expectedOutput, fread($this->outputStream, strlen($expectedOutput)));
    }

    public function provideYamlCases()
    {
        foreach (glob(dirname(__FILE__) . "/CompilerTestCases/*.yaml") as $file) {
            $parsed = Yaml::parse(file_get_contents($file), true);
            yield basename($file) => [
                $parsed['language'],
                $parsed['template'],
                $parsed['output']
            ];
        }
    }
}
