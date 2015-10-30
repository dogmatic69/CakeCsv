<?php
namespace CakeCsv\Lib;

use CakeCsv\Database\Schema\CsvTable;
use Cake\Utility\Inflector;
use Cake\Datasource\ConnectionManager;

class Table
{

/**
 * Connection instance
 *
 * @var ConnectionInterface
 */
    protected $_db = null;

    protected $_file = null;

    public function __construct(File $File, array $config = [])
    {
        $this->_file = $File;
        $config = array_merge([
            'connection' => 'default',
        ], $config);

        $this->_db = ConnectionManager::get($config['connection']);
    }

    public function tableNameFromFileName()
    {
        $filename = trim($this->_file->getBasename($this->_file->getExtension()), '.');
        return Inflector::slug(Inflector::underscore(Inflector::classify($filename)), '_');
    }

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

    protected function _dropTable($Csv) 
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