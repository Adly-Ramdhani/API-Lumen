<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{

    public function __construct()
   {
    $this->middleware('auth:api');
   }

    //melihata data
    public function index()
    {
        try{
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage);
        }
    }
    
    //membuat akun
    public function register(Request $request)
    {
        try{

        $username= $request->input('username');
        $email = $request->input('email');
        $password = Hash::make($request->input('passowrd'));
        $role = $request->input('role');

        $register = User::create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role'=> $request->$role
        ]);
        return ApiFormatter::sendResponse(200, true, 'Successfully Create A User Data', $register);
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
       
    }

    //melihat data sesuai id
    public function show(user $stuff, $id)
    {
        try{
            $data = User::where('id', $id)->first();

            if(is_null($data)){
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found');
            }else{
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
        
    }

    public function trash()
    {
        try {
            $data = User::onlyTrashed() -> get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err -> getMessage());
        }
    }

    //untuk mengembalikan data sesuai id
    public function restore(user $stuff, $id)
    {
        try{
            $checkProses = User::onlyTrashed()->where('id', $id)->restore();
    
            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            }else{
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data!');
            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

   

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required',
                'password' => 'required',
                'role'=> 'required'
            ]);

            $data = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => hash::make($request->password),
                'role' => $request->role

            ]);

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }


    //untuk update data
    public function update(Request $request, $id)
    {
        try{
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required',
                'role' => 'required',
                
            ]);
           
            if($request->password){
            $checkProses = User::where('id', $id)->update([
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,
                'password' => hash::make($request->password)
                
            ]);
        }else{
            $checkProses = User::where('id', $id)->update([
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,  
            ]);
        }

            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            }else{
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data!');
            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    //menghapus data sesuai id
    public function destroy(user $stuff, $id)
    {
        try{
            $checkProses= User::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    //untuk menghapus data permanen sesuai id
    public function deletePermanent($id)
    {
        try{
            $checkProses= User::where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus permanen');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function login(Request $request)
    {
        try{
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ],[
                'email.required' => 'Email harus di isi',
                'password.required' => 'Password harus di isi',
                'password.min' => 'Password minimal 8 karakter',
            ]);

            $user = User::where('email', $request->email)->first();

            if(!$user) {
                return ApiFormatter::sendResponse(400, false , 'Login Failed! User Dosent Exist');
            }else{
                $isValid = Hash::check($request->password,$user->password);

                if(!$isValid){
                    return ApiFormatter::sendResponse(400, false , 'Login Failed! Password Dosent Match');
                }else{
                    $generateToken = bin2hex(random_bytes(40));

                    $user->update([
                        'token' => $generateToken
                    ]);
                    return ApiFormatter::sendResponse(200, 'Login Succdssfully', $user);
                }
            }
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, false , $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try{
            $this->validate($request, [
                'email' => 'required',
                
            ]);

            $user = User::where('email', $request->email)->first();

            if(!$user) {
                return ApiFormatter::sendResponse(400, false , 'Login Failed! User Dosent Exist');
            }else{
                if(!$user->token){
                    return ApiFormatter::sendResponse(400, false , 'Logout Failed! User Doesnt Login Scine');
                }else{
                    $logout = $user->update(['token' => null]);

                    if($logout){
                    return ApiFormatter::sendResponse(200, 'Logout Succdssfully');
                    }
                }
            }
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }
}
