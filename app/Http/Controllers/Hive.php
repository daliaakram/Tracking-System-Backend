<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require_once  'C:\Users\nadag\Desktop\tracking-system\Back-End\trackingSystem\vendor\tranch-xiao\php-thrift-impala/ThriftSQL.phar';

class Hive extends Controller
{


    public function HiveConnection(Request $request)
    {
        $hive = new \ThriftSQL\Hive( '192.168.1.15', 10000, 'root', '' );
       $hiveTables = $hive ->connect() ->getIterator( 'SHOW TABLES' );

    foreach( $hiveTables as $rowNum => $row ) {
      print_r( $row );
    }

    }


}