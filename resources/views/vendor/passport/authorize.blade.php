@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-fused-buttons drop-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3"><b>Demande d'autorisation</b></h5>

                    <!-- Introduction -->
                    <p><b>{{ $client->name }}</b> requiert votre permission pour accéder à votre compte.</p>

                    <!-- Scope List -->
                    @if (count($scopes) > 0)
                        <div>
                                <p><b>Cette application pourra :</b></p>

                                <ul>
                                    @foreach ($scopes as $scope)
                                        <li>{{ $scope->description }}</li>
                                    @endforeach
                                </ul>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent p-0">
                    <div class="row m-0">
                        <div class="col-6 p-0">
                            <!-- Cancel Button -->
                            <form method="post" action="/oauth/authorize">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <button class="btn btn-primary text-danger w-100 left">Refuser</button>
                            </form>
                        </div>
                        <div class="col-6 p-0">
                            <!-- Authorize Button -->
                            <form method="post" action="/oauth/authorize">
                                {{ csrf_field() }}

                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <button type="submit" class="btn btn-primary w-100 right">Autoriser</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection