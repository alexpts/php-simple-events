<?php

namespace test\unit\Events\Behavior;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PTS\Events\Events;
use PTS\Events\StopPropagation;

/**
 * @covers \PTS\Events\Events::emit()
 */
class EmitTest extends TestCase
{
    const TEST_CLASS = Events::class;
    const TEST_METHOD = 'emit';

    /**
     * @return \ReflectionMethod
     */
    protected function getReflectionMethod(): \ReflectionMethod
    {
        $reflection = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $reflection->setAccessible(true);

        return $reflection;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @dataProvider dataProvider
     */
    public function testStopPropagationException(string $name, array $arguments): void
    {
        /** @var MockObject|Events $events */
        $events = $this->getMockBuilder(self::TEST_CLASS)
            ->disableOriginalConstructor()
            ->setMethods(['trigger'])
            ->getMock();

        $events->expects($this->once())->method('trigger')->with($name, $arguments)
            ->willThrowException(new StopPropagation);

        $events->emit($name, $arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @dataProvider dataProvider
     */
    public function testCallTrigger(string $name, array $arguments): void
    {
        /** @var MockObject|Events $events */
        $events = $this->getMockBuilder(self::TEST_CLASS)
            ->disableOriginalConstructor()
            ->setMethods(['trigger'])
            ->getMock();

        $events->expects($this->once())->method('trigger')->with($name, $arguments);
        $events->emit($name, $arguments);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            ['preSave', []],
            ['preSave', [1]],
            ['preSave', [1, '2']],
        ];
    }
}
