<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tickets;
use App\Models\EstadoTickets;
use App\Models\Departamento;
use App\Models\Urgencia;
use App\Http\Resources\TicketsResource;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;

class TicketsController extends Controller
{
    public function getAllTickets(Request $request) {
        try{
            $user = \Auth::user();
            $tickets = Tickets::with(['estadoTicket', 'departamento', 'user', 'responsable', 'urgencia'])->where('id_estado_ticket', "!=", 3);
                       
            if($user->role != 1){
                $tickets->where('id_responsable', $user->id);
            }

            if($request->fecha_inicio){
                $tickets->whereDate('created_at', '>=', $request->fecha_inicio);
            }
            if($request->fecha_fin){
                $tickets->whereDate('created_at', '<=', $request->fecha_fin);
            }

            if($request->fecha_fianlizacion_inicio){
                $tickets->whereDate('fecha_finalizacion', '>=', $request->fecha_fianlizacion_inicio);
            }
            if($request->fecha_fianlizacion_fin){
                $tickets->whereDate('fecha_finalizacion', '<=', $request->fecha_fianlizacion_fin);
            }

            if($request->estado != null){
                $tickets->where('id_estado_ticket', $request->estado);
            }

            if($request->departamento != null){
                $tickets->where('id_departamento', $request->departamento);
            }

            if($request->urgencia != null){
                $tickets->where('id_urgencia', $request->urgencia);
            }

            if($request->cliente != null){
                $tickets->where('id_usuario', $request->cliente);
            }

            if($request->responsable != null){
                $tickets->where('id_responsable', $request->responsable);
            }

            if($request->search != null){
                $tickets->where('descripcion', 'LIKE', '%'.$request->search.'%')->orWhere('created_at', 'LIKE', '%'.$request->search.'%');
            }

            $tickets->selectRaw('*, 
                (CASE 
                    WHEN DATE(fecha_finalizacion) = CURDATE() THEN 3 
                    WHEN id_urgencia = 1 THEN 2
                    WHEN fecha_finalizacion IS NOT NULL THEN 1
                    ELSE 0
                END) as order_score'
            )
            ->orderBy('order_score', 'desc')
            ->orderBy('fecha_finalizacion', 'asc'); // Ordena la fecha finalización en orden ascendente

            if($request->itemsPerPage != 0){
                $tickets = $tickets->paginate($request->itemsPerPage ?? 15);
                return TicketsResource::collection($tickets);
            }else{
                $tickets = $tickets->orderBy('created_at', 'DESC')->get();
                $data = TicketsResource::collection($tickets);
                $path = base_path();
                return response()->json([
                    'data' => $data,
                    'meta' => [
                        'current_page'  => 1,
                        'from'          => 1,
                        'last_page'     => 1,
                        'path'          => $path.'/api/get-tickets',
                        'per_page'      => 0,
                        'to'            => 0,
                        'total'         => $tickets->count()
                    ]
                ]);
            }

            // return response()->json(["code" => 200, "success" => $data]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }

    public function getTicket($id) {
        try{
            $tickets = Tickets::where('id', $id)->first();

            return response()->json(["code" => 200, "success" => $tickets]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }

    public function getEstadoTickets() {
        try{
            $estado_tickets = EstadoTickets::all();
            return response()->json(["code" => 200, "success" => $estado_tickets]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }

    public function getDepartamentos() {
        try{
            $departamentos = Departamento::all();
            return response()->json(["code" => 200, "success" => $departamentos]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }

    public function getUrgencia() {
        try{
            $urgencia = Urgencia::all();
            return response()->json(["code" => 200, "success" => $urgencia]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }

    public function createTicket(Request $request) {
        try{
            $ticket_new = Tickets::create( 
                [
                    "descripcion" => $request->descripcion,
                    "id_estado_ticket" => $request->id_estado_ticket ?? 1, // id_estado_ticket 1 = pendiente
                    "id_departamento" => $request->id_departamento,
                    "id_usuario" => $request->id_usuario,
                    "id_urgencia" => $request->id_urgencia,
                    "id_responsable" => $request->id_responsable ?? 1287, // id = 1287 es Maria Jesus Garcia
                    "fecha_finalizacion" => $request->fecha_finalizacion,
                ]
            );

            $ticket = Tickets::with(['user', 'departamento', 'urgencia'])->find($ticket_new->id);
            Mail::to("info@fidiaspro.com")->send(new TicketMail($ticket, 'create'));
            
            return response()->json(["code" => 200, "success" => "Operación exitosa"]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }

    public function updateTicket(Request $request) {
        try{
            $ticket = Tickets::with(['user'])->find($request->id);
            $ticket->descripcion = $request->descripcion;
            $ticket->id_estado_ticket = $request->id_estado_ticket;
            $ticket->id_departamento = $request->id_departamento;
            $ticket->id_usuario = $request->id_usuario;
            $ticket->id_urgencia = $request->id_urgencia;
            $ticket->id_responsable = $request->id_responsable;
            $ticket->fecha_finalizacion = $request->fecha_finalizacion;
            $ticket->save();

            if($request->fecha_finalizacion != null || $request->fecha_finalizacion != ''){
                $email = isset($ticket->user->email) ? $ticket->user->email : '';
                if($email != ''){
                    Mail::to($email)->send(new TicketMail($ticket, 'update'));
                }
            }
            
            return response()->json(["code" => 200, "success" => "Operación exitosa"]);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }
    
    public function deleteTicket($id){
        try{
            $ticket = Tickets::find($id);
            $ticket->delete();
            return response()->json(["code" => 200, 'success' => 'Operación exitosa']);
        }catch(\Exception $e){
            return response()->json(["code" => 400, "error" => $e->getMessage()]);
        }
    }
}
