@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Users</strong><a href="{{ route('adduser') }}"><button class="btn btn-success float-right"> + </button></a></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th ><strong> Name </strong></th>
                                <th ><strong> Email </strong></th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <form method="post" action="{{ route('edituser', ['id' => $user->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary"><i class="fas fa-pen"></i></button></td>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="{{ route('deluser', ['id' => $user->id]) }}" onSubmit="return confirm('User {{$user->name}} will be delete, are you sure?')">
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
