<?php
namespace CakeCsv\Database\Schema;

use Cake\Database\Schema\Table;

/**
 * CsvTable
 *
 * Class for creating tables based on a CSV file
 *
 * @package dogmatic69.CakeCSV.Database
 */
class CsvTable extends Table {

/**
 * Constructor
 * 
 * @param string $table the table name to be created
 * @param array $columns the list of columns to be created
 */
    public function __construct($table, array $columns = []) {
        parent::__construct($table, $columns);

        $this->options([
            'engine' => 'InnoDB',
            'collate' => 'utf8mb4_unicode_ci',
        ]);
    }

/**
 * Create table from the headers
 *
 * @param array $headers the headers from the CSV file used to create the database
 * 
 * @return CsvTable
 */
    public function fromHeader(array $headers) 
    {
        $this->addColumn('id', [
            'type' => 'string',
            'length' => 36,
        ])->addConstraint('primary', [
            'type' => 'primary',
            'columns' => ['id']
        ]);

        foreach ($headers as $header) {
            $this->addColumn($header, [
                'type' => 'text',
                'null' => true,
                'default' => null,
            ]);
        }

        return $this;
    }
}