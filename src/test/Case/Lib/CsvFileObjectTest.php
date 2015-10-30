<?php
namespace CakeCsv\Test\TestCase\Lib;

/**
 * CsvFileObjectTest
 */
class CsvFileObjectTest extends CakeTestCase
{

    /**
     * start up method
     *
     * @return void
     */
    public function setUp()
    {
        $this->path = CakePlugin::path('CakeCsv') . 'Test' . DS . 'Fixture' . DS;
        parent::setUp();
    }

    /**
     * tear down method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * test headings
     *
     * @return void
     */
    public function testHeading()
    {
        $Csv = new CsvFileObject($this->path . 'file1.csv');

        $this->assertTrue($Csv->hasHeadings());
        $expected = [
            [
                'field_1' => '1a',
                'field_2' => '2a///\\\\\\',
                'field_3' => '3a'
            ],
            [
                'field_1' => '1b\'\'\'',
                'field_2' => '2b”””',
                'field_3' => '3b'
            ],
            [
                'field_1' => '1c',
                'field_2' => '2c,,,',
                'field_3' => '3c'
            ],
        ];
        $headings = [
            'field_1',
            'field_2',
            'field_3'
        ];

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);

            $result = $Csv->headings();
            $this->assertEquals($headings, $result);
        }

        foreach ($expected as &$v) {
            $v = array_values($v);
        }
        array_unshift($expected, [
            'Field_1',
            'Field 2',
            'FIELD-3'
        ]);
        $headings = [];
        $Csv = new CsvFileObject($this->path . 'file1.csv', [
            'heading' => false
        ]);

        $this->assertFalse($Csv->hasHeadings());

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);

            $result = $Csv->headings();
            $this->assertEquals($headings, $result);
        }
    }

    /**
     * test rewind
     *
     * @return void
     */
    public function testRewind()
    {
        $Csv = new CsvFileObject($this->path . 'file1.csv');
        $expected = [
            [
                'field_1' => 'Field_1',
                'field_2' => 'Field 2',
                'field_3' => 'FIELD-3'
            ],
            [
                'field_1' => '1a',
                'field_2' => '2a///\\\\\\',
                'field_3' => '3a'
            ],
            [
                'field_1' => '1b\'\'\'',
                'field_2' => '2b”””',
                'field_3' => '3b'
            ],
            [
                'field_1' => '1c',
                'field_2' => '2c,,,',
                'field_3' => '3c'
            ],
        ];

        $result = $Csv->read();
        $this->assertEquals($expected[1], $result);

        $result = $Csv->read();
        $this->assertEquals($expected[2], $result);

        $Csv->rewind();

        $result = $Csv->read();
        $this->assertEquals($expected[0], $result);
    }

    /**
     * test reading csv files
     *
     * @return void
     */
    public function testRead()
    {
        $Csv = new CsvFileObject($this->path . 'file1.csv');
        $expected = [
            [
                'field_1' => '1a',
                'field_2' => '2a///\\\\\\',
                'field_3' => '3a'
            ],
            [
                'field_1' => '1b\'\'\'',
                'field_2' => '2b”””',
                'field_3' => '3b'
            ],
            [
                'field_1' => '1c',
                'field_2' => '2c,,,',
                'field_3' => '3c'
            ],
        ];

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);
        }

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals([], $Csv->read());
        }
    }

    /**
     * test reading with a default model defined
     *
     * @return void
     */
    public function testReadDefaultModel()
    {
        $Csv = new CsvFileObject($this->path . 'file1.csv', [
            'model' => 'MyModel'
        ]);
        $expected = [
            [
                'MyModel' => [
                    'field_1' => '1a',
                    'field_2' => '2a///\\\\\\',
                    'field_3' => '3a',
                ],
            ],
            [
                'MyModel' => [
                    'field_1' => '1b\'\'\'',
                    'field_2' => '2b”””',
                    'field_3' => '3b',
                ],
            ],
            [
                'MyModel' => [
                    'field_1' => '1c',
                    'field_2' => '2c,,,',
                    'field_3' => '3c',
                ],
            ],
        ];

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);
        }

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals([], $Csv->read());
        }
    }

    /**
     * test read different models
     *
     * @return void
     */
    public function testReadDifferentModels()
    {
        $Csv = new CsvFileObject($this->path . 'file2.csv');
        $expected = [
            [
                'Model' => [
                    'field' => '1a',
                ],
                'OtherModel' => [
                    'field' => '2a///\\\\\\',
                ],
                'SomeModel' => [
                    'field' => '3a',
                ],
                'nomodel' => 1,
            ],
            [
                'Model' => [
                    'field' => '1b\'\'\'',
                ],
                'OtherModel' => [
                    'field' => '2b”””',
                ],
                'SomeModel' => [
                    'field' => '3b',
                ],
                'nomodel' => 2,
            ],
            [
                'Model' => [
                    'field' => '1c',
                ],
                'OtherModel' => [
                    'field' => '2c,,,',
                ],
                'SomeModel' => [
                    'field' => '3c',
                ],
                'nomodel' => 3,
            ],
        ];

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);
        }

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals([], $Csv->read());
        }
    }

    /**
     * test read different models custom
     *
     * @return void
     */
    public function testReadDifferentModelsCustom()
    {
        $Csv = new CsvFileObject($this->path . 'file2.csv', [
            'model' => 'MyModel'
        ]);
        $expected = [
            [
                'Model' => [
                    'field' => '1a',
                ],
                'OtherModel' => [
                    'field' => '2a///\\\\\\',
                ],
                'SomeModel' => [
                    'field' => '3a',
                ],
                'MyModel' => [
                    'nomodel' => 1,
                ],
            ],
            [
                'Model' => [
                    'field' => '1b\'\'\'',
                ],
                'OtherModel' => [
                    'field' => '2b”””',
                ],
                'SomeModel' => [
                    'field' => '3b',
                ],
                'MyModel' => [
                    'nomodel' => 2,
                ],
            ],
            [
                'Model' => [
                    'field' => '1c',
                ],
                'OtherModel' => [
                    'field' => '2c,,,',
                ],
                'SomeModel' => [
                    'field' => '3c',
                ],
                'MyModel' => [
                    'nomodel' => 3,
                ],
            ],
        ];

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);
        }

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals([], $Csv->read());
        }

        $Csv = new CsvFileObject($this->path . 'file2.csv', [
            'model' => 'OtherModel',
        ]);
        $expected = [
            [
                'Model' => [
                    'field' => '1a',
                ],
                'OtherModel' => [
                    'field' => '2a///\\\\\\',
                    'nomodel' => 1,
                ],
                'SomeModel' => [
                    'field' => '3a',
                ],
            ],
            [
                'Model' => [
                    'field' => '1b\'\'\'',
                ],
                'OtherModel' => [
                    'field' => '2b”””',
                    'nomodel' => 2,
                ],
                'SomeModel' => [
                    'field' => '3b',
                ],
            ],
            [
                'Model' => [
                    'field' => '1c',
                ],
                'OtherModel' => [
                    'field' => '2c,,,',
                    'nomodel' => 3,
                ],
                'SomeModel' => [
                    'field' => '3c',
                ],
            ],
        ];

        for ($i = 0; $i < 3; $i++) {
            $result = $Csv->read();
            $this->assertEquals($expected[$i], $result);
        }

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals([], $Csv->read());
        }
    }
}
