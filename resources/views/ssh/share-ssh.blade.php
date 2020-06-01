@extends('layouts.app')

@section('content')
<div id="container" class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Connect to SSH Session</div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Id SSH Connection</label>

                        <div class="col-md-6">
                            <input id="sshconnection" type="text" class="form-control" name="sshconnection" value="" autocomplete="none" autofocus>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" onclick="connectSsh()" class="btn btn-primary">Connect</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div name="idconn" id="idconn"></div>
<div id="terminal" style="width:100%; height:90vh"></div>
@endsection

@push('scripts')
    <script>
    var resizeInterval;
    var wSocket;
    var password;
    var idconnection;
    var intervalId;

    function connectSsh() {
        wSocket = new WebSocket("ws:{{$websocketurl}}:8090");
        term.open(document.getElementById('terminal'));

        idconnection = document.getElementById("sshconnection").value;
        document.getElementById("container").style.display="none";
        document.getElementById("terminal").style.visibility="visible";
        document.getElementById("idconn").innerHTML = "Your are viewing the ssh connection with id " + idconnection;

        var dataSend = {"sharessh":
                        {
                        "idconnection" : idconnection,
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
