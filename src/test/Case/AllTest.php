<?php

namespace CakeCsv\Test\TestCase;

/**
 * AllCakeCsvTestsTest
 */
class AllCakeCsvTestsTest extends PHPUnit_Framework_TestSuite
{

    /**
     * Suite define the tests for this suite
     *
     * @return CakeTestSuite
     */
    public static function suite()
    {
        $suite = new CakeTestSuite('All CakeCsv test');
        $path = CakePlugin::path('CakeCsv') . 'Test' . DS . 'Case' . DS;
        $suite->addTestDirectoryRecursive($path);
        return $suite;
    }
}
