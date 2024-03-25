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
    //    try{
    //     $data = StuffStock::all()->toArray();

    //     return Apiformatter::sendResponse(200, 'succes', $data)
    //    }catch(\Exception $err){
    //     return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage)
    //    }
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
                    'total_available' => 'required'
                    'total_defec' => 'required'
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
}
