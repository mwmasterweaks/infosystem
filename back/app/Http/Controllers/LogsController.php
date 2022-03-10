<?php

namespace App\Http\Controllers;

use View;
use File;
use Response;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function downloadJSONFile()
    {
        $data = json_encode(['Example 1', 'Example 2', 'Example 3',]);
        $fileName = time() . '_datafile.json';

        //File::put(public_path('/upload/json/' . $fileName), $data);
        // return Response::download(public_path('/upload/jsonfile/' . $fileName));
    }
}
