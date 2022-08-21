<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FavoriteRequest;
use App\Models\Chat;
use App\Models\MatchUser;
use App\Models\Notification;
use App\Models\Picture;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;

class AdminsController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function login(Request $request){
		$data = $request->only("email", "password");
		
		if(Auth::attempt($data)){
			return redirect()->route("home");
		}
			
		return redirect()->route("index");	
		
	}

    public function home(){
		$user = Auth::user();
		dd($user);
	}

    public function logout(){
		Auth::logout();
		return redirect()->route("index");	
	}

    public function getAllPics(){
        $pics =  DB::table('pictures')
        ->where('pending', '=', 1)
        ->get();

        return json_encode($pics);
    }

    public function approvePic(Request $request){
        $pic = DB::table('pictures')
        ->where('id', $request -> id)
        ->update(['pending' => 1]);

        return "picture approved";
    }

    public function declinePic(Request $request){
        Picture::where('pictures.id', '=', $request -> id)
        ->delete();

        return "picture deleted";
    }
}
