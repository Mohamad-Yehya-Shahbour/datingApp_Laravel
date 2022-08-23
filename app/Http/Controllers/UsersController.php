<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FavoriteRequest;
use App\Models\Chat;
use App\Models\MatchUser;
use App\Models\Notification;
use App\Models\Picture;
use Illuminate\Support\Facades\DB;
use Validator;



class UsersController extends Controller
{
    public function getUsers(){

        $userGender = auth()->user()->gender; // get user gender
        $userId = auth()->user()->id; // get user id

        $favoriteRequestId = FavoriteRequest::where('favorite_requests.from', '=', $userId)
        ->pluck('to')->all();  // getting ids of people who favorite user

        // get people who are matched with user
        $matchedForUserId_1 = MatchUser::where('matches.user_1', '=', $userId)->pluck('user_2')->all();
        $matchedForUserId_2 = MatchUser::where('matches.user_2', '=', $userId)->pluck('user_1')->all();
        $matchedUsers = array_merge($matchedForUserId_1,$matchedForUserId_2);

        // selecting users who are not matched or already been favorited with opposite gender & not admin 
        $users = User::whereNotIn('id', $favoriteRequestId)
        ->whereNotIn('id', $matchedUsers)
        ->select("*")
        ->where('gender', '!=', $userGender)
        ->where('userType', '!=', '1')
        ->get();

        return json_encode($users, JSON_PRETTY_PRINT);

    }

    public function updateUser(Request $request){

        // validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'age' => 'required|string|between:2,100',
            'gender' => 'required|string|between:1,100',
            'description' => 'required|string|between:2,100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        $userId = auth()->user()->id;
        // update user information
        $user = DB::table('users')
              ->where('id', $userId)
              ->update(['name' => $request -> name,
                        'age' => $request -> age,
                        'gender' => $request -> gender,
                        'description' => $request -> description]);

    
        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user
        ], 201);
    }

    public function search(Request $request){

        $userGender = auth()->user()->gender; // get user gender
        $userId = auth()->user()->id; // get user id

        $favoriteRequestId = FavoriteRequest::where('favorite_requests.from', '=', $userId)
        ->pluck('to')->all(); // getting ids of people who favorite user

        // get people who are matched with user
        $matchedForUserId_1 = MatchUser::where('matches.user_1', '=', $userId)->pluck('user_2')->all();
        $matchedForUserId_2 = MatchUser::where('matches.user_2', '=', $userId)->pluck('user_1')->all();
        $matchedUsers = array_merge($matchedForUserId_1,$matchedForUserId_2);

        // selecting users who are not matched or already been favorited with opposite gender & not admin based on search-input 
        $users = User::whereNotIn('id', $favoriteRequestId)
        ->whereNotIn('id', $matchedUsers)
        ->select("*")
        ->where('gender', '!=', $userGender)
        ->where('userType', '!=', '1')
        ->where('name', 'Like', '%' . $request -> key . '%')
        ->get();

        return json_encode($users, JSON_PRETTY_PRINT);
    }

    public function addFavorite(Request $request){
        // check uf userid in to  -> if yes (remove the record where the userid is in to) & add record to matches table & add to notification table from userid to requestid
        // if no add to notification table from userid to requestid

        $userId = auth()->user()->id; 
        $userName = auth()->user()->name;

        // get every instance where user has been favorited before
        $favoriteRequestId = FavoriteRequest::where('favorite_requests.to', '=', $userId)->where('favorite_requests.from', '=', $request -> id)->pluck('from')->all();

        // id not favorited before...
        if(empty($favoriteRequestId)){

            // add record to favorite table
            $fav = new FavoriteRequest;
            $fav -> from = $userId;
            $fav -> to = $request -> id;
            $fav -> save();

            // send notification to the favorited user
            $notif = new Notification;
            $notif -> from = $userId;
            $notif -> to = $request -> id;
            $notif -> body = $userName." has favorited you";
            $notif -> save();

        return response()->json(['message' => 'request sent successfully'], 200); 

        }else{

            // delete from favorite table already existing record
            FavoriteRequest::where('favorite_requests.to', '=', $userId)->where('favorite_requests.from', '=', $request -> id)->delete();

            // add both users to matches table
            $match = new MatchUser;
            $match -> user_1 = $userId;
            $match -> user_2 = $request -> id;
            $match -> save();

            // send notification to the matched user
            $notif = new Notification;
            $notif -> from = $userId;
            $notif -> to = $request -> id;
            $notif -> body = "you have matched with ". $userName;
            $notif -> save();

            return response()->json(['message' => 'added to matches'], 200);
        }
    }

    public function addMessage(Request $request){

        // add record to messages, waiting for admin to accept
        $userId = auth()->user()->id;
        $msg = new Chat;
        $msg -> from = $userId;
        $msg -> to = $request -> to;
        $msg -> body = $request -> body;
        $msg -> pending = 0;
        $msg -> save();

        return response()->json(['message' => 'request is pending'], 200);

    }

    public function getMatched(){

        // get all matched users
        $userId = auth()->user()->id;
        $users = DB::table('matches')
        ->where('user_1', '=', $userId)
        ->orWhere('user_2', '=', $userId)
        ->get();

        return json_encode($users);
    }

    public function getUserPics(){

        // get all user pics
        $userId = auth()->user()->id;
        $pics = DB::table('pictures')
        ->where('user_id', '=', $userId)
        ->where('pending', '=', 1)
        ->get();

        return json_encode($pics);
    }
}
