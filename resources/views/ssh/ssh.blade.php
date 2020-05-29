@extends('layouts.app')

@section('content')
<div id="container" class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Connect to server {{$name}}</div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Decryption Password</label>

                        <div class="col-md-6">
                            <input id="decryptpassword" type="password" class="form-control" name="decryptpassword" value="" autocomplete="none" autofocus>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" onclick="decrypt()" class="btn btn-primary">Connect</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="terminal" style="width:100%; height:90vh"></div>
@endsection

@push('scripts')
    <script>
    var resizeInterval;
    var wSocket;
    var password;
    var intervalId;

    function connectServer() {
        wSocket = new WebSocket("ws:{{$websocketurl}}:8090");
        term.open(document.getElementById('terminal'));

        document.getElementById("container").style.display="none";
        document.getElementById("terminal").style.visibility="visible";

        var dataSend = {"auth":
                        {
                        "server":"{{$ip}}",
                        "port":"{{$port}}",
                        "user":"{{$user}}",
                        "password":password,
                        }
                    };

        term.fit();
        term.focus();

        wSocket.onopen = function (event) {
            console.log("Socket Open");

            term.attach(wSocket,false,false);
            wSocket.send(JSON.stringify(dataSend));
            intervalId = window.setInterval(function(){
                wSocket.send(JSON.stringify({"refresh":""}));
            }, 700);
        };

        wSocket.onerror = function (event){
            alert("Connection Closed");
            term.detach(wSocket);
            window.clearInterval(intervalId);
        };

        term.on('data', function (data) {
            var dataSend = {"data":{"data":data}};
            wSocket.send(JSON.stringify(dataSend));
            //Xtermjs with attach dont print zero, so i force. Need to fix it :(
            if (data=="0"){
            term.write(data);
            }
        });
    }

    async function decrypt() {
        passwordDecrypt = document.getElementById("decryptpassword").value;
        const pwUtf8 = new TextEncoder().encode(passwordDecrypt);                                  // encode password as UTF-8
        const pwHash = await crypto.subtle.digest('SHA-256', pwUtf8);                       // hash the password

        ciphertext = "{{$password}}";
        const iv = ciphertext.slice(0,24).match(/.{2}/g).map(byte => parseInt(byte, 16));   // get iv from ciphertext

        const alg = { name: 'AES-GCM', iv: new Uint8Array(iv) };                            // specify algorithm to use

        const key = await crypto.subtle.importKey('raw', pwHash, alg, false, ['decrypt']);  // use pw to generate key

        const ctStr = atob(ciphertext.slice(24));                                           // decode base64 ciphertext
        const ctUint8 = new Uint8Array(ctStr.match(/[\s\S]/g).map(ch => ch.charCodeAt(0))); // ciphertext as Uint8Array

        plaintext = "";
        try {   
            const plainBuffer = await crypto.subtle.decrypt(alg, key, ctUint8);
            password = new TextDecoder().decode(plainBuffer);                            // decode password from UTF-8
            connectServer();
        } catch(error) {
            alert("Decrypt Password Incorrect");
        }
    }

    //Execute resize with a timeout
    window.onresize = function() {
        clearTimeout(resizeInterval);
        resizeInterval = setTimeout(resize, 400);
    }
    // Recalculates the terminal Columns / Rows and sends new size to SSH server + xtermjs
    function resize() {
        if (term) {
        term.fit()
        }
    }
    </script>
@endpush
