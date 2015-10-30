<?php
namespace CakeCsv\Lib;

use CakeCsv\Lib\File;

/**
 * Iterator
 *
 * @package dogmatic69.CakeCsv.Lib
 */

class Iterator implements \Iterator
{

    /**
     * the current row from the csv file
     *
     * @var array
     */
    protected $_currentRow;

    /**
     * count of rows in the csv file
     *
     * @var int
     */
    protected $_rowCounter;

    /**
     * the CsvFileObject being used
     *
     * @var CsvFileObject
     */
    protected $_CsvFileObject;

    /**
     * set up the options for reading the csv file
     *
     * @param File $File the csv file to read
     */
    public function __construct(File $File)
    {
        $this->_CsvFileObject = $File;
    }

    /**
     * rewind the Iterator to the begining
     *
     * @return void
     */
    public function rewind()
    {
        $this->_rowCounter = 0;
        $this->_currentRow = [];
        $this->_CsvFileObject->rewind();

        if ($this->_CsvFileObject->hasHeadings()) {
            $this->_CsvFileObject->read();
        }
    }

    /**
     * get the current row of the csv file
     *
     * @return array
     */
    public function current()
    {
        if (empty($this->_currentRow) && $this->valid()) {
            $this->_currentRow = $this->_CsvFileObject->read();
        }

        return $this->_currentRow;
    }

    /**
     * get the key for the current row
     *
     * If the csv file has headings the counter is returned as $normalCount - 1
     *
     * @return int
     */
    public function key()
    {
        return $this->_rowCounter;
    }

    /**
     * go to the next row
     *
     * @return bool
     */
    public function next()
    {
        $this->_currentRow = [];
        $this->current();

        if ($this->valid()) {
            return $this->_rowCounter++;
        }

        return $this->_rowCounter;
    }

    /**
     * check if the current row is valid
     *
     * @return bool
     */
    public function valid()
    {
        return !$this->_CsvFileObject->eof();
    }
}
