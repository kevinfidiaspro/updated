<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ApiControllers\AuthenticationController;

Route::get('crear-tabla-chat', 'MigrationController@crear_tabla_chat');
Route::get('crear-tabla-mensajes', 'MigrationController@crear_tabla_mensajes');
Route::get('add-device-token', 'MigrationController@add_device_token');
Route::get('tansform-to-polimorfic', 'MigrationController@tansform_to_polimorfic');
Route::get('/tests', 'TestController@index');

Route::get('/', function () {
    return view('base');
});

Route::get('checklist-no-te-conoce-ni-el-tato', 'EncuestaController@showEncuesta');
Route::post('enviar-encuesta', 'EncuestaController@postEncuesta')->name('submit.encuesta');

Route::get('symlink', 'SymLinkController@create');

Route::get('crear-directorio', function(){
  return Storage::makeDirectory('public/lotes');
});
Route::post('/update-password', [AuthenticationController::class, 'UpdatePassword'])
                ->middleware('guest')
                ->name('password.update');
Route::get('/clear', function() {
   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('config:cache');
   Artisan::call('view:clear');
   return "Cleared!";
});
