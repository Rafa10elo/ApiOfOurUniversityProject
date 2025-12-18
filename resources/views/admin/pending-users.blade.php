@extends('layouts.app')

@section('content')
    <div class="container mt-5">

        <div class="row mb-4">
            <div class="col-md-4">
                <a href="{{ route('admin.pending') }}">
                    <div class="card text-center" style="background:#DDDAD0;border:2px solid #444326;">
                        <div class="card-body"><h5>Pending</h5></div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('admin.verified') }}">
                    <div class="card text-center" style="background:#DDDAD0;border:2px solid #444326;">
                        <div class="card-body"><h5>Verified</h5></div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('admin.rejected') }}">
                    <div class="card text-center" style="background:#DDDAD0;border:2px solid #444326;">
                        <div class="card-body"><h5>Rejected</h5></div>
                    </div>
                </a>
            </div>
        </div>

        @if(session('success'))
            <script>alert("{{ session('success') }}"); location.reload();</script>
        @endif
        @if(session('error'))
            <script>alert("{{ session('error') }}"); location.reload();</script>
        @endif

        <div class="card" style="background:#DDDAD0;">
            <div class="card-header text-white" style="background:#444326;">
                Pending Users
            </div>

            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead style="background:#444326;color:white;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imagesModal{{ $user->id }}">
                                    {{ $user->phone }}
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('admin.verify',$user->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm text-white" style="background:#444326;">Verify</button>
                                </form>

                                <form action="{{ route('admin.reject',$user->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm" style="border:1px solid #444326;">Reject</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="imagesModal{{ $user->id }}">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background:#444326;">
                                        <h5>{{ $user->first_name }} {{ $user->last_name }}</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row text-center">
                                            <div class="col-md-6">
                                                <h6>Profile</h6>
                                                <img src="{{ $user->getFirstMediaUrl('profile_image') }}"
                                                     class="img-fluid border">
                                            </div>
                                            <div class="col-md-6">
                                                <h6>ID</h6>
                                                <img src="{{ $user->getFirstMediaUrl('id_image') }}"
                                                     class="img-fluid border">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
