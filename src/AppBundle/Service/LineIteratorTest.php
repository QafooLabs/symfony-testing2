<?php

namespace AppBundle\Service;

abstract class LineIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIterateEachLine()
    {
        $iterator = $this->createIteratorWithLineAndContent(3, 'foo');

        $lines = iterator_to_array($iterator);

        $this->assertEquals(['foo', 'foo', 'foo'], $lines);
    }

    public function testIteratorSkipsEmptyLines()
    {
        $iterator = $this->createIteratorWithLineAndContent(1, '');

        $lines = iterator_to_array($iterator);

        $this->assertCount(0, $lines);
    }

    abstract public function createIteratorWithLineAndContent($number, $lineContent);
}

class StringLineIteratorTest extends LineIteratorTest
{
    public function createIteratorWithLineAndContent($number, $lineContent)
    {
        return new StringLineIterator(
            rtrim(str_repeat($lineContent . "\n", $number))
        );
    }

    public function testRequiresString()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new StringLineIterator(array());
    }
}

interface LineIterator extends \IteratorAggregate
{
}

class StringLineIterator implements LineIterator
{
    private $lines;

    /**
     * @param string $lines
     */
    public function __construct($lines)
    {
        if (!is_string($lines)) {
            throw new \InvalidArgumentException();
        }
        $this->lines = $lines;
    }

    public function getIterator()
    {
        $lines = explode("\n", $this->lines);
        $lines = array_filter($lines, function ($line) { return strlen($line) > 0; });
        return new \ArrayIterator($lines);
    }
}














