<?php
namespace CakeCsv\Lib;

use CakeCsv\Database\Schema\CsvTable;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

/**
 * Table
 *
 * @package dogmatic69.CakeCsv.Lib
 */
class Table
{

    /**
     * Connection instance
     *
     * @var ConnectionInterface
     */
    protected $_db;

    /**
     * Instance of the File object
     *
     * @var File
     */
    protected $_file;

    /**
     * Constructor
     *
     * @param File $File the file instance
     * @param array $config the config to be used
     */
    public function __construct(File $File, array $config = [])
    {
        $this->_file = $File;
        $config = array_merge([
            'connection' => 'default',
        ], $config);

        $this->_db = ConnectionManager::get($config['connection']);
    }

    /**
     * Figure out the table name from the file name
     *
     * @return string
     */
    public function tableNameFromFileName()
    {
        $filename = trim($this->_file->getBasename($this->_file->getExtension()), '.');
        return Inflector::slug(Inflector::underscore(Inflector::classify($filename)), '_');
    }

    /**
     * Create the table for the CSV file
     *
     * @param bool $dropFirst set to true to try drop before creation
     *
     * @return string
     */
    public function createSchema($dropFirst = false)
    {
        $tableName = $this->tableNameFromFileName();
        $Csv = new CsvTable($tableName);

        $Csv->fromHeader($this->_file->rawHeadings());
        if ($dropFirst) {
            $this->_dropTable($Csv);
        }

        foreach ($Csv->createSql($this->_db) as $sql) {
            $this->_db->execute($sql);
        }

        return $tableName;
    }

    /**
     * Try and drop the given table
     *
     * @param CsvTable $Csv the CsvTable instance
     *
     * @return bool
     */
    protected function _dropTable(CsvTable $Csv)
    {
        foreach ($Csv->dropSql($this->_db) as $sql) {
            try {
                $this->_db->execute($sql);
            } catch (\PDOException $e) {
            }
        }
        return true;
    }
}
