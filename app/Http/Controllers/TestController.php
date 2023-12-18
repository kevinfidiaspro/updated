<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Images\HandleImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{
    public function index(){
    	return view("tests");
       $p = 'A:/laragon/www/apase2/storage/app/public/documentos/userId_2/documentacion_general/vlcsnap2021032922h14m40s456_1617254542.png';
       return Response::download($p);
        // return HandleImages::images();
    }
}
