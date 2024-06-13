<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\InboundStuff;
use App\Models\StuffStock;
use App\Models\Stuff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

 
class InboundStuffController extends Controller
{
    public function __construct()
   {
    $this->middleware('auth:api');
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        try{
            if($request->filter_id){
               $data = InboundStuff::where('stuff_id', 'name', $request->filter_id)->with('stuff','stuff.stuffStock')->get();
            }else{
                $data = InboundStuff::all();
            }
            return Apiformatter::sendResponse(200, 'succes', $data);
           }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request',$err->getMessage());
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
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            $chekStuff = Stuff::where('id', $request->stuff_id)->first();
            if(!$chekStuff){
                return ApiFormatter::sendResponse(400, false ,'Data Stuff does not exiet');
            }else{
                if($request->hasFile('proff_file')) {
                    $proff = $request->file('proff_file');
                    $destinationPath = 'proff/';
                    $proffName = date('YmdHis') . "." . $proff->getClientOriginalExtension();
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
                            'total_defec' => 0,
                        ]);
                    } else {
                        $updateStock = $getStuffStock->update([
                            'stuff_id' => $request->stuff_id,
                            'total_available' =>$getStuffStock['total_available'] + $request->total,
                            'total_defec' => $getStuffStock['total_defec'],
                        ]);
                    }
    
                    if ($updateStock) {
                        $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                        $stuff = [
                            'stuff' => $getStuff,
                            'InboundStuff' => $createStock,
                            'stuffStock' => $getStock
                        ];
    
                        return ApiFormatter::sendResponse(200, 'Successfully Create A Inbound Stuff Data', $stuff);
                    } else {
                        return ApiFormatter::sendResponse(400, false, 'Failed To Update A Stuff Stock Data');
                    }
                } else {
                }
            } 
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
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
        try {
            $getLending = inboundStuff::with('stuff','stuffStock')->find();

            if(!$getLending){
                return ApiFormatter::sendResponse(404,false, 'data lending not found');
            }else{
                return ApiFormatter::sendResponse(200,true,'succes get lending data', $getLending);
            }
        }catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
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
    public function update(Request $request, $id)
    {
        try {
            $getInboundStuff = InboundStuff::find($id);

            if (!$getInboundStuff) {
                return ApiFormatter::sendResponse(404, false, 'Data inbound Stuff Not Found');
            }else{
                $this->validate($request, [
                    'stuff_id' => 'required',
                    'total' => 'required',
                    'date' => 'required',
                ]);

                if ($request->hasFile('proff_file')) {
                    $proof = $request->file('proff_file');
                    $destinationPath = 'proff/';
                    $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension();
                    $proof->move($destinationPath, $proofName);

                    // unlink(base_path('public/proff/' . $getInboundStuff
                    // ['proff_file']));
                }else {$proofName = $getInboundStuff['proff_file'];
                }

                $getStuff = Stuff::where('id', $getInboundStuff['stuff_id'])->first();
                $getStuffStock = StuffStock::where('stuff_id', $getInboundStuff['stuff_id'])->first();
                //request tidak berubah

                $getCurrenStock = StuffStock::where('stuff_id', $request['stuff_id'])->first();

                if ($getStuffStock['stuff_id'] == $request['stuff_id']) {
                    $updateStock = $getStuffStock->update([
                        'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'] + $request->total,
                    ]);
                }else {
                    $updateStock = $getStuffStock->update([
                        'total_available' => $getStuffStock['total_available'] - $getInboundStuff['total'],
                    ]);

                    $updateStock = $getCurrenStock->update([
                        'total_available' => $getStuffStock['total_available'] + $request['total'],
                    ]);
                }
                $updateInbound = $getInboundStuff->update([
                    'stuff_id' => $request->stuff_id,
                    'total' => $request->total,
                    'date' => $request->date,
                    'proff_file' => $proofName,  
                ]);

                $getStock = StuffStock::where('stuff_id', $request['stuff_id'])->first();
                $getInbound = InboundStuff::find($id)->with('stuff', 'stuffStock');
                $getCurrentStuff = Stuff::where('id', $request['stuff_id'])->first();

                $stuff = [
                    'stuff' => $getCurrentStuff,
                    'InboundStuff' => $getInbound,
                    'stuffStock' => $getStock,
                ];

                return ApiFormatter::sendResponse(200, true, 'Successfully Update A Inbound Stuff Data', $stuff);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function destroy(InboundStuff $inboundStuff, $id)
    {
        try {
            $checkProses = InboundStuff::where('id', $id)->first();
    
            if ($checkProses) {
                $dataStock = StuffStock::where('stuff_id', $checkProses->stuff_id)->first();
                if ($dataStock->total_available < $checkProses->total) {
                    return ApiFormatter::sendResponse(400, 'bad request', 'Total available kurang dari total data di pinjam');
                } else {
                    $stuffId = $checkProses->stuff_id;
                    $totalInbound = $checkProses->total;
                    $checkProses->delete();
    
                    $dataStock = StuffStock::where('stuff_id', $checkProses->stuff_id)->first();
                    
                    if ($dataStock) {
                        $total_available = (int)$dataStock->total_available - (int)$totalInbound;
                        $minusTotalStock = $dataStock->update(['total_available' => $total_available]);
    
                        if ($minusTotalStock) {
                            $updateStufAndInbound = Stuff::where('id', $stuffId)->with('inboundStuffs', 'stuffStock')->first();
                            return ApiFormatter::sendResponse(200, 'success', $updateStufAndInbound);
                        }
                    } else {
                        // Handle if stock data is not found
                        return ApiFormatter::sendResponse(404, 'not found', 'Data stok stuff tidak ditemukan');
                    }
                }
            } else {
                // Handle if InboundStuff data is not found
                return ApiFormatter::sendResponse(404, 'not found', 'Data InboundStuff tidak ditemukan');
            }
        } catch (\Exception $err) {
            // Handle exception
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    

    public function trash()
    {
        try{
            $data= InboundStuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore(InboundStuff $inboundStuff, $id)
    {
        try {
            // Memulihkan data dari tabel 'inbound_stuffs'
            $checkProses = InboundStuff::onlyTrashed()->where('id', $id)->restore();
    
            if ($checkProses) {
                // Mendapatkan data yang dipulihkan
                $restoredData = InboundStuff::find($id);
    
                // Mengambil total dari data yang dipulihkan
                $totalRestored = $restoredData->total;
    
                // Mendapatkan stuff_id dari data yang dipulihkan
                $stuffId = $restoredData->stuff_id;
    
                // Memperbarui total_available di tabel 'stuff_stocks'
                $stuffStock = StuffStock::where('stuff_id', $stuffId)->first();
                
                if ($stuffStock) {
                    // Menambahkan total yang dipulihkan ke total_available
                    $stuffStock->total_available += $totalRestored;
    
                    // Menyimpan perubahan pada stuff_stocks
                    $stuffStock->save();
                }
    
                return ApiFormatter::sendResponse(200, 'success', $restoredData);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function deletePermanent(InboundStuff $inboundStuff, Request $request, $id)
    {
        try {
            // Mencari data yang belum dihapus (tidak di trash)
            $getInbound = InboundStuff::where('id', $id)->first();
    
            if ($getInbound === null) {
                return ApiFormatter::sendResponse(404, 'not found', 'Data inbound-stuff tidak ditemukan');
            }
    
            $filePath = base_path('public/proff/' . $getInbound->proff_file);
            if (file_exists($filePath)) {
                unlink($filePath);
            } else {
                return ApiFormatter::sendResponse(404, 'not found', 'File tidak ditemukan');
            }
    
            $checkProses = $getInbound->forceDelete();
    
            if ($checkProses) {
                return ApiFormatter::sendResponse(200, 'success', 'Data inbound-stuff berhasil dihapus permanen');
            } else {
                return ApiFormatter::sendResponse(500, 'internal server error', 'Gagal menghapus data dari database');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    
    
    
  
             
 } 

