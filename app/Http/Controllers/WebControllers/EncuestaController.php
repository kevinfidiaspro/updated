<?php

namespace App\Http\Controllers\WebControllers;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResultadoEncuestaInfo;
use App\Mail\ResultadoEncuesta;
use Illuminate\Http\Request;

class EncuestaController extends Controller
{
    public function showEncuesta(){
      $items = $this->getItems();
      return view('encuesta.encuesta', compact('items'));
    }

    public function postEncuesta(Request $request){
      $email = $request->user_email;

      $items_seleccionados = count($request->except(['_token', 'user_email']));

      $resultado = $this->getResults($items_seleccionados);

      $resultados_admin = $this->getAdminResults($request->except(['_token', 'user_email']));

      Mail::to($email)->send(new ResultadoEncuesta($resultado));

      Mail::to('info@fidiaspro.com')->send(new ResultadoEncuestaInfo($resultado, $resultados_admin, $email));

      return Redirect::to('https://fidiaspro.com/gracias-checklist');
    }

    private function getResults($count){
      if($count < 5){
        return 0;
      }
      if($count <= 10){
        return 1;
      }
      if($count > 10){
        return 2;
      }
    }

    private function getAdminResults($results){
      $items_on = array_keys($results);

      $items = $this->getItems();

      return collect($items)->map(function ($item) use ($items_on) {
          return [
            'text'  => $item['text'],
            'value' => in_array($item['value'], $items_on) ? 'Si' : 'No'
          ];
      });
    }

    private function getItems(){
      return $items = [
        [
          'text'  => 'Para mi el cliente es lo primero',
          'value' => 'item_uno'
        ],
        [
          'text'  => 'Contesto las reseñas de mis clientes',
          'value' => 'item_dos'
        ],
        [
          'text'  => 'Mi perfil de RRSS y Google My Business está completado al 100%',
          'value' => 'item_tres'
        ],
        [
          'text'  => 'Mi web es segura y funciona rápido',
          'value' => 'item_cuatro'
        ],
        [
          'text'  => 'Actualizo frecuentemente el stock de mi tienda online',
          'value' => 'item_cinco'
        ],
        [
          'text'  => 'Mi web tiene blog con contenido de utilidad',
          'value' => 'item_seis'
        ],
        [
          'text'  => 'Estoy en las redes sociales que mi negocio necesita',
          'value' => 'item_siete'
        ],
        [
          'text'  => 'Publico en las redes sociales con regularidad',
          'value' => 'item_ocho'
        ],
        [
          'text'  => 'Utilizo hashtag y palabras clave',
          'value' => 'item_nueve'
        ],
        [
          'text'  => 'Me preocupo por la estética de mi web y mis redes sociales',
          'value' => 'item_diez'
        ],
        [
          'text'  => 'He invertido en campañas online de pago',
          'value' => 'item_once'
        ],
        [
          'text'  => 'Mido y analizo mis resultados',
          'value' => 'item_doce'
        ],
        [
          'text'  => 'Investigo y analizo a mi competencia',
          'value' => 'item_trece'
        ],
        [
          'text'  => 'No envío tantos emails publicitarios como para aparecer en SPAM',
          'value' => 'item_catorce'
        ],
      ];
    }
}
