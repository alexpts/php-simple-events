<?php
namespace PTS\Events;

class FiltersTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filters */
    protected $filters;

    protected function setUp()
    {
        $this->filters = new Filters();
    }

    /**
     * @param string $value
     * @param int $length
     * @return string
     */
    public function customFilterHandler($value, $length = 4)
    {
        return substr($value, 0, $length);
    }


    public function testGetListeners()
    {
        $listeners1 = $this->filters->getListeners();
        $this->filters->on('some:id', 'trim');
        $listeners2 = $this->filters->getListeners();

        $this->assertCount(0, $listeners1);
        $this->assertCount(1, $listeners2);
    }

    public function testSimpleFilter()
    {
        $title = ' Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $title = $this->filters->filter('before_output_title', $title);

        $this->assertEquals(trim($title), $title);
    }

    public function testFilterWithoutListeners()
    {
        $rawTitle = ' Hello world!!!  ';
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals($rawTitle, $title);
    }

    public function testFilterWithExtraEmitArguments()
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler']);
        $title = $this->filters->filter('before_output_title', $rawTitle, [5]);
        $this->assertEquals('Hello', $title);
    }

    public function testFilterWithExtraOnArguments()
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 50, [5]);
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals('Hello', $title);
    }


    public function testOffHandlerWithPriority()
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->off('before_output_title', 'trim', 30);
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals($rawTitle, $title);
        $this->assertCount(0, $this->filters->getListeners());
    }

    public function testOffHandlerWithoutPriority()
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->off('before_output_title', 'trim');
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals($rawTitle, $title);
        $this->assertCount(0, $this->filters->getListeners());
    }

    public function testOffAllHndlers()
    {
        $rawTitle = 'Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim');
        $this->filters->off('before_output_title');
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals($rawTitle, $title);
        $this->assertCount(0, $this->filters->getListeners());
    }

    public function testOrderHandler()
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 20);
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals('Hell', $title);
    }

    public function test2OrderHandler()
    {
        $rawTitle = '  Hello world!!!  ';
        $this->filters->on('before_output_title', 'trim', 30);
        $this->filters->on('before_output_title', [$this, 'customFilterHandler'], 40);
        $title = $this->filters->filter('before_output_title', $rawTitle);
        $this->assertEquals('He', $title);
    }

    public function testChain()
    {
        $expected = __NAMESPACE__ . '\FiltersInterface';
        $this->assertInstanceOf($expected, $this->filters->on('some', 'trim'));
        $this->assertInstanceOf($expected, $this->filters->off('some', 'trim'));
    }
}