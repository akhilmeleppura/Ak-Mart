<?php

namespace App\Http\Controllers\WriteToTestFile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
  
use Illuminate\Support\Facades\File;


class WriteToTestFile extends Controller
{
    public static function exec($content)
    {
        //TEST START

        $filePath = public_path('test.txt');
        //$data = "Hello, World!";
        File::put($filePath, ($content));

        //TEST END
    }
}

