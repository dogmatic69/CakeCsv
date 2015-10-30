# CakeCsv plugin for CakePHP

This plugin is for doing all sorts of data manipulations using CSV, TSV and similar file formats.

It is able to take a CSV file and optionally create a database table based on the header rows. Data can also be imported directly into an existing database table.

Data can also be exported to file from the database, using the specified escape chars, line endings etc.


## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require dogmatic69/CakeCsv
```

### Versions

This repo holds versions for cake3 and cake2. Make sure to require the correct version.

## Usage

### Read a CSV file

The file object is a wrapper for `\SplFileObject` with some added methods to change settings and check what headings are available.

```
use CakeCsv\Lib\File;
$File = new File($patToCsvFile);
```

The lib is able to map data to different models based on the header row, for example to save a member and profile data in one (this uses saveAssociated)

```
Member.id, Member.name, MemberProfile.image
```

### Write CSV File

Use options to adjust the format of the CSV file, eg: `delimiter`, `enclosre`, `nulls` etc.

```
use CakeCsv\Lib\Array;

$Array = new Array($pathToCsvFile, $options);
$Array->headings([
    'field_a',
    'field_b',
]);


// add some rows and append to the file
$Array->rows([
    [
        'row-1-a',
        'row-1-b'
    ],
    [
        'row-2-a',
        'row-2-b'
    ],
]);
$Array->append();

// or from the model

$Array->rows(Hash::extract($Model->find('all'), '{n}.ModelName'));
$Array->write();

// or direct write
$Array->write(Hash::extract($Model->find('all'), '{n}.ModelName'));
```

### Iterate

Its possible to read in massive CSV files using this as it will only read a single line at a time. This class extends `\Iterator`

```
use CakeCsv\Lib\File;
use CakeCsv\Lib\Iterator;
$Iterator new Iterator(new File($pathToCsvFile));

for ($Iterator->rewind(); $Iterator->valid(); $Iterator->next())
{
    $data = $Iterator->current();
}
```

### Create table from CSV file

Its possoble to create a database table based on the file if it has headers. The headers will be made into something usable for the database and then a schema generated. Its also possible to get a model class that can be used to then manipulate the data.

Currently all tables are created as text, there is no introspection to figure out a better field for different data types. On larger files it would take a while to parse the entire file and figure out what to make each field. It might be possible to do this after the table has been generated and data inserted.

```
use CakeCsv\Lib\Table;
use CakeCsv\Lib\File;
$Csv = new File($patToCsvFile);
$Table = new Table($Csv, [
    'connection' => 'custom_connection',
]);

$tableName = $Table->createSchema(true);

// Get an instance of the model created from the CSV file
$CsvTable = TableRegistry::get(Inflector::classify($tableName), [
    'connection' => ConnectionManager::get('custom_connection'),
]);
```


## Roadmap

- More tests, currently they are still 2.x format
- Data type detection
- view for display, similar to JsonView
