<?php
namespace CakeCsv\Lib;

use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * CsvFileObject for reading csv data from files
 *
 * This creates a SplFileObject class suited for reading / parsing csv files. It
 * allows passing all the options required and configures the object with
 * SplFileObject::setCsvControl().
 *
 * See CsvFileObject::__construct() for the defaults and options that can be passed
 *
 * @see http://php.net/manual/en/class.splfileobject.php
 * @see http://www.php.net/manual/en/splfileobject.setcsvcontrol.php
 *
 * @package dogmatic69.CakeCsv.Lib
 */

class File extends \SplFileObject
{

    /**
     * does the csv file contain a heading as the first row
     *
     * @var bool
     */
    protected $_hasHeading;

    /**
     * internal cache of the headings
     *
     * @var array
     */
    protected $_headings = [];

    /**
     * the default model this data belongs to
     *
     * @var string
     */
    protected $_model = null;

    /**
     * set up the csv iterator object
     *
     * $settings can contain the following:
     *  - mode: @see http://www.php.net/manual/en/function.fopen.php
     *  - include_path: boolean Whether to search in the include_path for filename.
     *  - delimiter: the csv field delimiter (default ,)
     *  - enclosure: the csv field enclosure (default ")
     *  - escape: the csv data escape char (default \)
     *  - heading: boolean, true if first row is headings, false if not (default true)
     *
     * To take advantage of this classes functionality, even though it extends SplFileObject
     * for reading csv files, that method should not be used direcly but instead only use
     * CsvFileObject::read()
     *
     * @param string $file the file to be loaded
     * @param array $settings the config for the csv file
     *
     * @return void
     */
    public function __construct($file, $settings = [])
    {
        $settings = array_merge([
            'mode' => 'r',
            'include_path' => false,
            'resource' => null,
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
            'heading' => true,
            'model' => null
        ], $settings);

        $this->_hasHeading = $settings['heading'];

        parent::__construct($file, $settings['mode'], $settings['include_path']);

        $this->setCsvControl($settings['delimiter'], $settings['enclosure'], $settings['escape']);

        $this->_model = $settings['model'];
        $this->headings();
    }

    /**
     * check if the file has headings
     *
     * @return bool
     */
    public function hasHeadings()
    {
        return $this->_hasHeading;
    }

    /**
     * get headings from the csv file
     *
     * If there are headings available (see the settings) this will get them and return
     * an array that has been fomatted to suite easy importing.
     *
     * @return array
     */
    public function headings()
    {
        if ($this->hasHeadings() && empty($this->_headings)) {
            $this->rewind();
            $this->_headings = $this->fgetcsv();

            foreach ($this->_headings as &$heading) {
                if (strstr($heading, '.') === false) {
                    $heading = [$this->_model, $heading];
                } else {
                    $heading = pluginSplit($heading);
                }

                $heading[0] = Inflector::classify($heading[0]);
                $heading[1] = Inflector::slug(strtolower($heading[1]));

                $heading = implode('.', array_filter($heading));
            }

            if (count($this->_headings) != count(array_unique($this->_headings))) {
                throw new Exception('Some headings are not unique (Case insensitive)');
            }
        }

        return $this->_headings;
    }

    /**
     * Get the raw headings for a csv data set
     *
     * @return array
     */
    public function rawHeadings()
    {
        if (!$this->hasHeadings()) {
            return [];
        }

        $this->rewind();
        $raw = $this->fgetcsv();

        if (count($raw) != count(array_unique($raw))) {
            throw new Exception('Some headings are not unique (Case insensitive)');
        }

        return $raw;
    }

    /**
     * overload the method to create array with heading => value
     *
     * @return array
     */
    public function read()
    {
        $row = $this->fgetcsv();
        if (count($row) === 1 && is_null(current($row)) || is_null($row)) {
            return [];
        }

        $headings = $this->headings();
        if (!empty($headings)) {
            $row = array_combine($headings, $row);
        }

        return Hash::expand($row);
    }
}
