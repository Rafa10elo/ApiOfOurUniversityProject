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
            <script>alert("{{ session('success') }}");</script>
        @endif

        <div class="card" style="background:#DDDAD0;">
            <div class="card-header text-white" style="background:#444326;">
                Verified Users
            </div>

            <div class="card-body">
                <table class="table table-bordered text-center align-middle">
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
                                <button class="btn btn-sm text-dark"
                                        style="border:1px solid #444326;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#removeModal{{ $user->id }}">
                                    Remove
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="imagesModal{{ $user->id }}">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background:#444326;">
                                        <h5>{{ $user->first_name }} {{ $user->last_name }}</h5>
                                    </div>
                                    <div class="modal-body text-center">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Profile</h6>
                                                @if($user->hasMedia('profile_image'))
                                                    <img src="{{ $user->getFirstMediaUrl('profile_image') }}"
                                                         class="img-fluid border">
                                                @else
                                                    <p>No Profile Image</p>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h6>ID</h6>
                                                @if($user->hasMedia('id_image'))
                                                    <img src="{{ $user->getFirstMediaUrl('id_image') }}"
                                                         class="img-fluid border">
                                                @else
                                                    <p>No ID Image</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="removeModal{{ $user->id }}">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background:#444326;">
                                        <h5>Confirm Remove</h5>
                                    </div>
                                    <div class="modal-body text-center">
                                        Are you sure you want to remove
                                        <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.remove', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">Remove</button>
                                        </form>
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
