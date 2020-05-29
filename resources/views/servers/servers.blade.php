@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Servers</strong><a href="{{ route('addserver') }}"><button class="btn btn-success float-right"> + </button></a></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th ><strong> Server </strong></th>
                                <th ><strong> IP Address </strong></th>
                                <th ><strong> Port </strong></th>
                                <th ><strong> User </strong></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach ($servers as $server)
                            <tr>
                                <td>{{ $server->name }}</td>
                                <td>{{ $server->ip }}</td>
                                <td>{{ $server->port }}</td>
                                <td>{{ $server->user }}</td>
                                <td>
                                    <form method="post" action="{{ route('connectssh', ['id' => $server->id]) }}" target="_blank">
                                    @csrf
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-satellite-dish"></i></button>
                                    </form>
                                <td>
                                    <form method="post" action="{{ route('editserver', ['id' => $server->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary"><i class="fas fa-pen"></i></button></td>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="{{ route('deleteserver', ['id' => $server->id]) }}" onSubmit="return confirm('Server {{$server->name}} will be delete, are you sure?')">
                                    @csrf
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
