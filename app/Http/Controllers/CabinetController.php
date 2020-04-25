<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\User;

class CabinetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('cabinet');
    }

    public function game()
    {
        return view('game');
    }

    public function show()
    {
        return new UserResource(User::where('id', auth()->id())->first());
    }

    public function save(Request $request)
    {
        User::where('id', auth()->id())->update(['name' => $request->name, 'email' => $request->email, 'gender' => $request->gender]);

        return response('saved', 200);
    }

    public function remove()
    {
        User::where('id', auth()->id())->delete();

        return response('deleted', 200);
    }

    public function avatar(Request $request)
    {
       $image = $request->image;
       $path = $image->store('uploads', 'public');
       if(isset($path) && !empty($path)) {
           User::where('id', auth()->id())->update(['avatar' => $path]);
       }
    }

    protected static function uploadImage($image, $path, $name){
        $file = $image;
        $ext = $file->getClientOriginalExtension();
        $file->move(public_path() . '/uploads/images/users/' . $path , $name . '.' . $ext);

        return '/uploads/images/users/' . $path . $name . '.' . $ext;
    }
}
