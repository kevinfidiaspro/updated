<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'      => $this->id,
      'unrread' => $this->unrread,
      $this->mergeWhen($this->user()->exists(), function() {
        return [
          'nombre'  => $this->user->nombre,
          'email'  => $this->user->email,
        ];
      })
    ];
  }
}
