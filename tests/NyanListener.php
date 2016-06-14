<?php

//namespace Mockery\Adapter\Phpunit;
namespace Wecamp\FlyingLiqourice;

class NyanListener implements \PHPUnit_Framework_TestListener
{
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        usleep(rand(20000, 100000));
    }

    /**
     * Add Mockery files to PHPUnit's blacklist so they don't showup on coverage reports
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }
    /**
     *  The Listening methods below are not required for Mockery
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }
}
