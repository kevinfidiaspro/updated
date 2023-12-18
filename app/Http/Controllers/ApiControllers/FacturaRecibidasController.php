<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacturaRecibida;
use Barryvdh\DomPDF\Facade as PDF;
use App\Traits\Files\HandlerFiles;
use App\Http\Requests\FacturaRecibidaRequest;

class FacturaRecibidasController extends Controller
{


    protected function pathServer(){
        $PATH = $_SERVER['DOCUMENT_ROOT'];
        $pathPublicOut = explode('public',$PATH);
        $res = $pathPublicOut[0]; 
        return $res;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idUser)
    {
        $facturaRecibidas = FacturaRecibida::orderBy('id', 'desc')->with('proveedor')->where('user_id', $idUser)->get();

        return response()->json([
            'status' => 200,
            'facturaRecibidas' => $facturaRecibidas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacturaRecibidaRequest $request)
    {
        $factRec = new FacturaRecibida;
        $factRec->user_id = $request->user_id;
        $factRec->proveedor_id = $request->proveedor_id;
        $factRec->descripcion = $request->descripcion;
        $factRec->fecha = $request->fecha;
        $factRec->save();

        $destination  = $this->pathServer() . "/storage/app/public/documentos/userId_" . $request->user_id . '/factura_recibidas/';
        $storeFiles = HandlerFiles::store($request, $destination);
        $storeFiles->original['nombresArchivos'];

        if (count($storeFiles->original['nombresArchivos']) > 0) {
          $fr = FacturaRecibida::findOrFail($factRec->id);
          $fr->imagen = $storeFiles->original['nombresArchivos'];
          $fr->update();
        }

        return response()->json($factRec, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fr = FacturaRecibida::find($id);

        return response()->json([

            'fr' => $fr
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $frU = FacturaRecibida::findOrFail($id);
        $frU->user_id = $request->user_id;
        $frU->proveedor_id = $request->proveedor_id;
        $frU->descripcion = $request->descripcion;
        $frU->fecha = $request->fecha;
        $frU->save();

        $destination  = $this->pathServer() . "/storage/app/public/documentos/userId_" . $request->user_id . '/factura_recibidas/';
        $storeFiles = HandlerFiles::store($request, $destination);
        $storeFiles->original['nombresArchivos'];

        if (count($storeFiles->original['nombresArchivos']) > 0) {
          $fr = FacturaRecibida::findOrFail($frU->id);
          $fr->imagen = $storeFiles->original['nombresArchivos'];
          $fr->update();

        }


        $frU = FacturaRecibida::find($id);
        return response()->json([

            'fr' => $frU
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fr = FacturaRecibida::find($id)->delete();
         return response()->json([

            'message' => 'Delete Successfully'
        ]);
    }
}
