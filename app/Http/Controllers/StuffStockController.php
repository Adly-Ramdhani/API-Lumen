<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\StuffStock;
use Illuminate\Http\Request;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       try{
        $data = StuffStock::with('stuff')->get();

        return Apiformatter::sendResponse(200, 'succes', $data);
       }catch(\Exception $err){
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage);
       }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        {
            try {
                $this->validate($request, [
                    'stuff_id' => 'required',
                    'total_available' => 'required',
                    'total_defec' => 'required',
                ]);
    
                $data = StuffStock::create([
                    'stuff_id' => $request->name,
                    'total_available' => $request->category,
                    'total_defec' => $request->category,
                ]);
    
                return ApiFormatter::sendResponse(200, 'success', $data);
            } catch (\Exception $err) {
                return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function show(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function edit(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StuffStock $stuffStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(StuffStock $stuffStock)
    {
        //
    }

    public function addStock(Request $request, $id)
    {
        try {

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ApiFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request,[
                    'total_available' => 'required',
                    'total_defec' => 'required',
                ]);

                $addStock = $getStuffStock->update([
                    $isStockAvailable = $getStuffStock['total_available'] + $request->total_available,
                    $isDefecAvailable = $getStuffStock['total_defec'] + $request->total_defec,

                ]);

                if ($addStock) {
                    $getStuffStockAdded = StuffStock::where('id', $id)->with('stuff')->first();

                    return ApiFormatter::sendResponse(200, true, 'Successfully Add A Stock Of Stuff Stock Data', $getStuffStockAdded);
                }
            }
            } catch (\Exception $err) {
            return ApiFormatter::sendResponse(500, false, $err->getMessage());
         }
    }  

    public function subStock(Request $request, $id)
    {
        try {

            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ApiFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request,[
                    'total_available' => 'required',
                    'total_defec' => 'required',
                ]);

                $isStockAvalible = $getStuffStock['total_available'] + $request->total_available;
                $isStockAvalible = $getStuffStock['total_defec'] + $request->total_defec;

                if($isStockAvalible < 0 || $isStokDefac < 0 ) {
                    return ApiFormatter::sendResponse(400, true, ' A Substraction Stock Can Less Than A Stock Stored', $getStuffStockAdded);
                }else{
                    $subStock = $getStuffStock->update([
                        'total_availble' => $isStockAvalible,
                        'total_defac' => $isStokDefac,

                    ]);

                    if ($subStock) {
                        $getStockSub= StuffStock::where('id', $id)->with('stuff')->first();
    
                        return ApiFormatter::sendResponse(200, true, 'Successfully Sub A Stock Of Stuff Stock Data', $getStockSub);
                    }
                } 
             }
            } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
         }
    }  
}
