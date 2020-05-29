<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();

        return view('users/users', ['users' => $users]);
    }

    public function adduser()
    {
        return view('users/adduser');
    }

    public function deluser($id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();
        $user->delete();

        return redirect('users');
    }

    public function createuser(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('users');
    }

    public function edituser($id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();
        return view('users/edituser', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
    }

    public function saveuser(Request $request)
    {
        $user = User::where('id', '=', $request->id)->firstOrFail();
        $user->name = $request->name;
        if ($request->password != "") {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect('users');
    }
}
