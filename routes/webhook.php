<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/test-sendinblue/{id}','FacebookWebhookController@TestSendinBlue');

Route::get('/callback-verification','FacebookWebhookController@CallbackVerification');
Route::post('/callback-verification','FacebookWebhookController@CallbackVerification');
Route::get('/getLeadData','FacebookWebhookController@getLeadData');
Route::post('/set-token','FacebookWebhookController@setToken');
Route::get('/get-token','FacebookWebhookController@getToken');
Route::get('/get-lead-fields','FacebookWebhookController@getLeadFieldData');
Route::get('/getLead','FacebookWebhookController@getLead');
Route::get('/fix-leads','FacebookWebhookController@FixLeads');
Route::get('/fix-forms/{id}','FacebookWebhookController@fixForms');
Route::get('/get-all-formularios','FacebookWebhookController@getFormularios');
Route::get('/get-all-formularios-activos','FacebookWebhookController@getFormulariosActivos');

Route::get('/get-formulario/{id}','FacebookWebhookController@getFormulario');
Route::post('/save-formulario','FacebookWebhookController@updateFormulario');
Route::get('/get-sending-blue-list/{page_id}','SendinBlueController@getList');
Route::get('/get-form-fields','FacebookWebhookController@FormFields');
Route::get('/get-pages','FacebookWebhookController@GetPages');
Route::get('/get-page/{id}','FacebookWebhookController@GetPage');
Route::post('/save-page','FacebookWebhookController@SavePage');
Route::get('/get-form-leads','FacebookWebhookController@GetLeadsFromForm');
Route::post('/fix-lead','FacebookWebhookController@FixLead');
Route::post('/get-long-lived-token','FacebookWebhookController@FacebookGetLongLivedToken');
Route::post('/get-page-access-token','FacebookWebhookController@FacebookGetPageAccessToken');
Route::post('/get-add-app','FacebookWebhookController@FacebookAddAppRequest');

Route::get('test-ciclo','FacebookWebhookController@getCurrentPotencial');

