<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\InboundStuff;
use App\Models\Stuff;
use App\models\StuffStock;
use Illuminate\Http\Request;

class InboundStuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try{
            $this->validate($request, [
                'stuff_id' => 'required'
                'total' => 'required'
                'date' => 'required'
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048'
            ]);

            if ($request->hashFile('prof_file')){
                $proff = $request->file('proff_file');
                $destinationPath = 'proff/';
                $proffName = date('YmdHis'). "." . $proff->getClienOriginalExtension();
                $proff->move($destinationPath, $proffName);
            }

            $createStock = InboundStuff::create([
                'stuff_id' => $request->stuff_id,
                'total' => $request->total,
                'date' => $request->date,
                'proff_file' => $proffName,
            ]);

            if ($createStock){
                $getStuff = Stuff::where('id', $request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

                if (!$getStuffStock){
                    $updateStock = StuffStock::create([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $request->total,
                        'total_defac' => 0,
                    ]);
                }else{
                    $updateStock = $getStuffStock->update([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defac' => $getStuffStock['total_defac'],
                    ]);
                }

                if($updateStock){
                    $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                    $stuff = [
                        'stuff' => $getStuff,
                        'inboundStuff' => $createStock,
                        'stuffStock' => $getStock,
                    ];
                    return ApiFormatter::sendResponse(200, true , 'Successfully cretae A inbound Stuff Data', $Stuff);
                }else{
                    return ApiFormatter::sendResponse(400, false , 'Failed Tp Update A Stuff Stock Data');
                }
            }else{
                 return ApiFormatter::sendResponse(400, false ,'Failed To Create A Inbound Stuff Data');
            }
        }catch(\Exception $e) {
            return ApiFormatter::sendResponse(400, false , $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function show(InboundStuff $inboundStuff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function edit(InboundStuff $inboundStuff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InboundStuff $inboundStuff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function destroy(InboundStuff $inboundStuff)
    {
        //
    }
}