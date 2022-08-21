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
}
