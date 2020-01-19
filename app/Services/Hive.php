<?php
namespace App\Services;
use Illuminate\Http\Request;
require_once __DIR__ . '/ThriftSQL.phar';

class Hive {
    
public function HiveConnection(Request $request)
{
    $hive = new \ThriftSQL\Hive( 'localhost', 10000, 'user', 'pass' );
$hiveTables = $hive
  ->connect()
  ->getIterator( 'SHOW TABLES' );

foreach( $hiveTables as $rowNum => $row ) {
  print_r( $row );
}

}


}