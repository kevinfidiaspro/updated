<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use App\Models\Marketing;
use Illuminate\Http\Request;
use App\Http\Requests\MarketingRequest;
use App\Http\Resources\MarketingResource;

class MarketingController  extends Controller
{
    public function deleteMarketingData(Request $request){
        $marketing = Marketing::find($request->id)->delete();
    }
    public function saveMarketingData(MarketingRequest $request){
        $arr = $request->all();
        $date_arr = explode('-',$request->mes);
        $arr['mes'] = $date_arr[0].'-'. $date_arr[1].'-01';
        $marketing = Marketing::updateOrCreate(['id'=>$request->id],$arr);
    }
    public function getMarketingData(Request $request){
        $marketing = Marketing::with('Web');
        if($request->id_web){
            $marketing->where('id_web',$request->id_web);
        }
        if($request->year){
            $marketing->whereYear('mes',$request->year);
        }
        if($request->amount == -1){
            $result = $marketing->get();
            return [
                'total'=>$marketing->count(),
                'data'=>  MarketingResource::collection( $result)
            ];
        }else{
            $result = $marketing->simplePaginate($request->amount??15);
            return [
                'total'=>$marketing->count(),
                'data'=>  MarketingResource::collection( $result)
            ];
        }
      
    }
}
