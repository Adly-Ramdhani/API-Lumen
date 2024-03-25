<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\ApiFormatter;
use App\Models\Lending;
use Illuminate\Http\Request;

class LendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data = Lending::all();

            return ApiFormatter::sendResponse(200, 'succes', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $derr->getMassage);
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
        $date = Carbon::now()->toDateString(); 
        try{
            $this->validate($request, [
                "stuff_id" => 'required',
                "date_time" => 'required|date',
                "name" => 'required',
                "user_id" => 'required',
                "notes" => 'required',
                "total_stuff" => 'required',
            ]);

            $data = Lending::create([
                "stuff_id" => $request->stuff_id,
                "date_time" => $request->input('date'),
                "name" => $request->name,
                "user_id" => $request->user_id,
                "notes" => $request->notes,
                "total_stuff" => $request->total_stuff,

            ]);

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
     }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function show(Lending $lending, $id)
    {
        try{
            $data = Lending::where('id', $id)->first();

            if(is_null($data)){
                return ApiFormatter::senResponse(400, 'data request', 'Data not found');
            }else{
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function edit(Lending $lending)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lending $lending, $id)
    {
        // ry{
        //     $this->validate($request, [
        //         'username' => 'required',
        //         'email' => 'required',
        //         'password' => 'required',
                
        //     ]);

        //     $checkProses = User::where('id', $id)->update([
        //         'username' => $request->username,
        //         'email' => $request->email,
        //         'password' => hash::make($request->password)
                

        //     ]);

        //     if($checkProses) {
        //         $data = User::find($id);
        //         return ApiFormatter::sendResponse(200, 'success', $data);
        //     }else{
        //         return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data!');
        //     }
        // }catch(\Exception $err){
        //     return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lending $lending)
    {
        //
    }
}
