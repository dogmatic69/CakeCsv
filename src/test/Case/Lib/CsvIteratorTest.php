<?php
namespace CakeCsv\Test\TestCase\Lib;

/**
 * CsvIteratorTest
 */
class CsvIteratorTest extends CakeTestCase
{

    /**
     * start up method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Csv = new CsvIterator(new CsvFileObject(
            CakePlugin::path('CakeCsv') . 'Test' . DS . 'Fixture' . DS . 'file1.csv'
        ));

        $file = CakePlugin::path('CakeCsv') . 'Test' . DS . 'Fixture' . DS . 'file1.csv';
        $this->CsvNoHeading = new CsvIterator(new CsvFileObject($file, [
            'heading' => false,
        ]));

        $this->expected = [
            [
                'field_1' => '1a',
                'field_2' => '2a///\\\\\\',
                'field_3' => '3a',
            ],
            [
                'field_1' => '1b\'\'\'',
                'field_2' => '2b”””',
                'field_3' => '3b',
            ],
            [
                'field_1' => '1c',
                'field_2' => '2c,,,',
                'field_3' => '3c',
            ],
        ];
    }

    /**
     * tear down method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Csv, $this->CsvNoHeading);
        parent::tearDown();
    }

    /**
     * test readings csv data
     *
     * @return void
     */
    public function testRead()
    {
        $result = $this->Csv->current();
        $this->assertEquals($this->expected[0], $result);

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[0], $result);

        $this->Csv->next();

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[1], $result);

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[1], $result);

        $this->Csv->next();

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[2], $result);

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[2], $result);
    }

    /**
     * test rewind
     *
     * @return void
     */
    public function testRewind()
    {
        $result = $this->Csv->current();
        $this->assertEquals($this->expected[0], $result);

        $this->Csv->next();
        $this->Csv->next();

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[2], $result);

        $this->Csv->rewind();

        $result = $this->Csv->current();
        $this->assertEquals($this->expected[0], $result);


        foreach ($this->expected as &$v) {
            $v = array_values($v);
        }
        array_unshift($this->expected, [
            'Field_1',
            'Field 2',
            'FIELD-3'
        ]);
        $result = $this->CsvNoHeading->current();
        $this->assertEquals($this->expected[0], $result);

        $this->CsvNoHeading->next();
        $this->CsvNoHeading->next();

        $result = $this->CsvNoHeading->current();
        $this->assertEquals($this->expected[2], $result);

        $this->CsvNoHeading->rewind();

        $result = $this->CsvNoHeading->current();
        $this->assertEquals($this->expected[0], $result);
    }

    /**
     * test key is returned correctly
     *
     * @return void
     */
    public function testKey()
    {
        $this->Csv->rewind();
        $this->assertEquals(0, $this->Csv->key());

        $this->Csv->next();
        $this->assertEquals(1, $this->Csv->key());

        $this->Csv->next();
        $this->assertEquals(2, $this->Csv->key());

        $this->Csv->next();
        $this->assertEquals(3, $this->Csv->key());

        $this->Csv->next();
        $this->assertEquals(3, $this->Csv->key());

        $this->Csv->rewind();
        $this->assertEquals(0, $this->Csv->key());


        $this->CsvNoHeading->rewind();
        $this->assertEquals(0, $this->CsvNoHeading->key());

        $this->CsvNoHeading->next();
        $this->assertEquals(1, $this->CsvNoHeading->key());

        $this->CsvNoHeading->next();
        $this->assertEquals(2, $this->CsvNoHeading->key());

        $this->CsvNoHeading->next();
        $this->assertEquals(3, $this->CsvNoHeading->key());

        $this->CsvNoHeading->next();
        $this->assertEquals(4, $this->CsvNoHeading->key());

        $this->CsvNoHeading->next();
        $this->assertEquals(4, $this->CsvNoHeading->key());

        $this->CsvNoHeading->rewind();
        $this->assertEquals(0, $this->CsvNoHeading->key());
    }

    /**
     * test valid
     *
     * @return void
     */
    public function testValid()
    {
        $this->Csv->rewind();

        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($this->Csv->valid());
            $this->Csv->next();
        }

        $this->Csv->next();
        $this->assertFalse($this->Csv->valid());

        $this->Csv->rewind();
        $this->assertTrue($this->Csv->valid());

        $this->CsvNoHeading->rewind();
        for ($i = 0; $i < 4; $i++) {
            $this->assertTrue($this->CsvNoHeading->valid());
            $this->CsvNoHeading->next();
        }

        $this->CsvNoHeading->next();
        $this->assertFalse($this->CsvNoHeading->valid());

        $this->CsvNoHeading->rewind();
        $this->assertTrue($this->CsvNoHeading->valid());
    }
}
