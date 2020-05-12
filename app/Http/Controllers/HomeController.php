<?php

namespace App\Http\Controllers;

use App\Events\SessionEvent;
use App\Http\Resources\UserResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        return UserResource::collection(User::all());
        return view('home');
    }

    public function getFriends()
    {
        return UserResource::collection(User::where('id', '!=', auth()->id())->get());
    }

    public function addVkId(Request $request)
    {
        User::where('id', auth()->id())->update(['vk_id' => $request->vk_id, 'vk_status' => $request->status]);

        return response('Saved', 200);
    }
}
