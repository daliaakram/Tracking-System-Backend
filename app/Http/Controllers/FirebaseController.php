<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class FirebaseController extends Controller
{
   protected $dbname = 'users';
   //protected $database;
    
    
    
    public function index()
    {
        
        $id = 1;     
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/trackerKey.json');
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://tracker-e91d2.firebaseio.com/')
        ->create();
        $database = $firebase->getDataBase();
        while (true)
        {
             $users = $database->getReference('users')->getChild('1')->getSnapshot()->getValue();
             print_r($users);
        }
       
     //  print_r($database->getReference('users')->getSnapshot()->getChild($id)->getValue());
      //  return $database->getReference('users')->getSnapshot()->getChild($id)->getValue();
     /*   $reference = $database->getReference('blog/post');
      
        $data = $reference->getData();
        return $data;*/
      /*  $newPost = $database
        ->getReference('blog/posts')
        ->push([
        'title' => 'Laravel FireBase Tutorial' ,
        'category' => 'Laravel'
        ]);
        echo '<pre>';
        print_r($newPost->getvalue());*/
   /* if(empty($id) || isset($id)) {
        return "false";
    }
        if ($database->getReference('users')->getSnapshot()->hasChild($id)->hasChild(1)){
           return $database->getReference('users')->getChild($id)->getValue()->toString();
       } else {
          // return FALSE;
            echo "false";
       }*/
    }
}



//   laravelfirebase-433e4