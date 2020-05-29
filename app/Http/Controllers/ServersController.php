<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servers;

class ServersController extends Controller
{
    public function index()
    {
        $servers = Servers::orderBy('name', 'asc')->get();
        return view('servers/servers', ['servers' => $servers]);
    }

    public function addserver()
    {
        return view('servers/addserver');
    }

    public function createserver(Request $request)
    {
        $server = array(
            'name' => $request->name,
            'ip' => $request->ip ,
            'port' => $request->port,
            'user' => $request->user,
            'password' => $request->realpassword
        );
        Servers::create($server);

        return redirect('servers');
    }

    public function deleteserver($id)
    {
        $server = Servers::where('id', '=', $id)->firstOrFail();
        $server->delete();

        return redirect('servers');
    }

    public function editserver($id)
    {
        $server = Servers::where('id', '=', $id)->firstOrFail();
        return view('servers/editserver', ['id' => $server->id, 'name' => $server->name, 'ip' => $server->ip,'port' => $server->port, 'user' => $server->user]);
    }

    public function saveserver(Request $request)
    {
        $server = Servers::where('id', '=', $request->id)->firstOrFail();
        $server->name = $request->name;
        $server->ip = $request->ip;
        $server->user = $request->user;
        $server->port = $request->port;

        if ($request->realpassword != "") {
            $server->password = $request->realpassword;
        }

        $server->save();

        return redirect('servers');
    }
}
