@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add Server</div>

                <div class="card-body">
                    <form method="POST" id="serverform" name="serverform" action="{{ route('createserver') }}">
                        @csrf

                        <input type="hidden" name="realpassword" id="realpassword" value="">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="" required autocomplete="off" autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ip" class="col-md-4 col-form-label text-md-right">IP Address</label>
                            <div class="col-md-6">
                                <input id="ip" type="text" class="form-control" name="ip" value="" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="port" class="col-md-4 col-form-label text-md-right">Port</label>
                            <div class="col-md-6">
                                <input id="port" type="number" min="0" class="form-control" name="port" value="22" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user" class="col-md-4 col-form-label text-md-right">User</label>
                            <div class="col-md-6">
                                <input id="user" type="text" class="form-control" name="user" value="" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" value="" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="encrypt" class="col-md-4 col-form-label text-md-right">Encrypt Password</label>
                            <div class="col-md-6">
                                <input id="encryptpaswword" type="password" class="form-control" name="encryptpaswword" value="" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-primary" onclick="encryptMessage()">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function encryptMessage() {
        const pwUtf8 = new TextEncoder().encode(document.getElementById("encryptpaswword").value); // encode password as UTF-8
        const pwHash = await crypto.subtle.digest('SHA-256', pwUtf8);                      // hash the password

        const iv = crypto.getRandomValues(new Uint8Array(12));                             // get 96-bit random iv

        const alg = { name: 'AES-GCM', iv: iv };                                           // specify algorithm to use

        const key = await crypto.subtle.importKey('raw', pwHash, alg, false, ['encrypt']); // generate key from pw

        const ptUint8 = new TextEncoder().encode(document.getElementById("password").value); // encode plaintext as UTF-8
        const ctBuffer = await crypto.subtle.encrypt(alg, key, ptUint8);                   // encrypt plaintext using key
    
        const ctArray = Array.from(new Uint8Array(ctBuffer));                              // ciphertext as byte array
        const ctStr = ctArray.map(byte => String.fromCharCode(byte)).join('');             // ciphertext as string
        const ctBase64 = btoa(ctStr);                                                      // encode ciphertext as base64

        const ivHex = Array.from(iv).map(b => ('00' + b.toString(16)).slice(-2)).join(''); // iv as hex string
        document.getElementById("realpassword").value = ivHex+ctBase64;

        document.getElementById("serverform").submit();
    }
</script>
@endpush
