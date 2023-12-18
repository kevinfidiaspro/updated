<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Artisan;
use Storage;
use Response;

class SymLinkController extends Controller
{
    //TODO Remove in production
    public function create()
    {
       $exitCode = Artisan::call('storage:link');
       return response("Command finished with exit code ${exitCode}");
    } 

    public function getVideo($video){
      $video = Video::find($video)->first();
      $fileContents = Storage::disk('videos')->get($video->file_name);
      $response = Response::make($fileContents, 200);
      $response->header('Content-Type', "video/mp4");
      return $response;
    }
}
