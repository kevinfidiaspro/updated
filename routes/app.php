<?php

use Illuminate\Support\Facades\Route;

Route::get('app-get-all-act-promo', 'AppPromocionController@getActivePromo');
Route::post('app-user-recover-password', 'AppUsuarioController@recover');

Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('app-get-avatar-by-user-id/{user_id}', 'AppUsuarioController@getAvatarByUserId');
  Route::get('app-get-proyectos-by-user-id/{user_id}', 'AppProyectoController@getProyectosByUserId');
  Route::get('app-get-est-proyecto-by-id/{proyecto_id}', 'AppProyectoController@getEstadoByProyectoId');
  Route::get('app-show-all-facturas-by-user/{user_id}', 'AppFacturaController@getFacturasByUserId');
  Route::get('app-show-facturas-by-proyecto/{proyecto_id}', 'AppFacturaController@getFacturasByProyecto');
  Route::get('app-show-facturas-by-proyecto/{proyecto_id}', 'AppFacturaController@getFacturasByProyecto');
});
