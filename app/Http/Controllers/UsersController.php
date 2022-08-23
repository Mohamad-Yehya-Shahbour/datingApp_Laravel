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
}
