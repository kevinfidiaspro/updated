<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ReciboEmail;

class MailController extends Controller
{
  public function sendEmail(Request $request){
    Mail::to($request->email)->send(new ReciboEmail($request->archivo));
    return response()->json($request->all, 200);
  }
}
