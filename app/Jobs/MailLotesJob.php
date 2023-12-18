<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MailLotesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected  $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        
        if($data['tipo'] == 'Facturas Enviadas'){
            foreach($data['emails'] as $email){
            Mail::send('emails.lotes', $data, function ($message) use ($data, $email){
                    $message->from($data['user']['email']); //From
                    $message->to($email)->subject($data['user']['name'] .' , '. $data['user']['nombre_fiscal'])
                    ->attach(storage_path('app/public/lotes/userId_' . $data['user']['id'] . '/facturas_enviadas.zip'));

                });
            }
        }

        if($data['tipo'] == 'Facturas Recibidas'){
            foreach($data['emails'] as $email){
            Mail::send('emails.lotes', $data, function ($message) use ($data, $email){
                    $message->from($data['user']['email']); //From
                    $message->to($email)->subject($data['user']['name'] .' , '. $data['user']['nombre_fiscal'])
                    ->attach(storage_path('app/public/lotes/userId_' . $data['user']['id'] . '/facturas_recibidas.zip'));

                });
            }
        }


        if($data['tipo'] == 'Todas'){
            foreach($data['emails'] as $email){
            Mail::send('emails.lotes', $data, function ($message) use ($data, $email){
                    $message->from($data['user']['email']); //From
                    $message->to($email)->subject($data['user']['name'] .' , '. $data['user']['nombre_fiscal'])
                    ->attach(storage_path('app/public/lotes/userId_' . $data['user']['id'] . '/facturas_recibidas.zip'))
                    ->attach(storage_path('app/public/lotes/userId_' . $data['user']['id'] . '/facturas_enviadas.zip'))
                    
                    ;

                });
            }
        }
        

       
    }
}
