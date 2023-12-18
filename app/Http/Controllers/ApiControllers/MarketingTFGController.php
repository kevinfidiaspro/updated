<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use App\Models\MarketingTFG;
use Illuminate\Http\Request;
use App\Http\Requests\MarketingTFGRequest;
use App\Http\Resources\MarketingTFGResource;
use Carbon\Carbon;
class MarketingTFGController extends Controller
{
    public function deleteMarketingData(Request $request){
        $marketing = MarketingTFG::find($request->id)->delete();
    }
    public function saveMarketingData(MarketingTFGRequest $request){
        $arr = $request->all();
        $date_arr = explode('-',$request->mes);
        $arr['mes'] = $date_arr[0].'-'. $date_arr[1].'-01';
        $marketing = MarketingTFG::updateOrCreate(['id'=>$request->id],$arr);
    }
    public function getMarketingDataDahsboard(){
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfCurrentMonth = Carbon::now()->endOfMonth();

        $models = MarketingTFG::whereBetween('mes', [$startOfLastMonth, $endOfCurrentMonth])->orderBy('mes','DESC')->get();
        return MarketingTFGResource::collection( $models);
    }
    public function getMarketingData(Request $request){
        $marketing = MarketingTFG::with('Web');
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
                'data'=>  MarketingTFGResource::collection( $result)
            ];
        }else{
            $result = $marketing->simplePaginate($request->amount??15);
            return [
                'total'=>$marketing->count(),
                'data'=>  MarketingTFGResource::collection( $result)
            ];
        }
     
    }
}
