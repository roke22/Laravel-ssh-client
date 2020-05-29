<?php

namespace App\Http\Controllers;

use App\Servers;
use Illuminate\Http\Request;

class SshController extends Controller
{
    public function connectssh($id)
    {
        $server = Servers::where('id', '=', $id)->firstOrFail();
        return view('ssh/ssh', [
            'name' => $server->name,
            'ip' => $server->ip,
            'port' => $server->port,
            'user' => $server->user,
            'password' => $server->password,
            'websocketurl' => env("WEBSOCKET_URL", "localhost"),
        ]);
    }
}
