<?php

namespace Resque\Component\Job\Tests\Model;

use Resque\Redis\Test\ResqueTestCase;
use Resque\Component\Job\Model\FilterableJobInterface;
use Resque\Component\Job\Model\Job;

class JobTest extends ResqueTestCase
{
    public function testCloneDropsId()
    {
        $job = new Job(
            'foo', ['arg' => 'bar']
        );
        $this->assertNotNull($job->getId());
        $clone = clone $job;
        $this->assertNotSame($job->getId(), $clone->getId());
        $this->assertNotNull($clone->getId());
    }

    /**
     * @dataProvider dataProviderMatchFilter
     */
    public function testMatchFilter(FilterableJobInterface $job, $expected, $filter)
    {
        $this->assertEquals($expected, $job::matchFilter($job, $filter));
    }

    public function dataProviderMatchFilter()
    {
        $args = [
            'baz' => 'test',
        ];
        $job = new Job('FooJob', $args);

        $jobId = $job->getId();

        return [
            [
                $job,
                false,
                null,
            ],
            [
                $job,
                false,
                [],
            ],
            [
                $job,
                true,
                ['id' => $jobId],
            ],
            [
                $job,
                false,
                ['id' => 'some-other-id'],
            ],
            [
                $job,
                false,
                ['id' => $jobId, 'class' => 'FuzzJob'],
            ],
            [
                $job,
                false,
                ['class' => 'FuzzJob'],
            ],
            [
                $job,
                true,
                ['id' => $jobId, 'class' => 'FooJob'],
            ],
            [
                $job,
                true,
                ['class' => 'FooJob'],
            ],
            [
                $job,
                false,
                ['id' => '123', 'class' => 'FooJob'],
            ],
        ];
    }
}
