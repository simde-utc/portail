@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-fused-buttons drop-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3">
                        {{ $client->name }} de l'association <strong>{{ \App\Models\Asso::find($client->asso_id)->name }}</strong> est réservé uniquement aux utilisateurs:
                    </h5>
                    <div class="row m-0">
                        <ul>
                            @foreach ($types as $type)
                                <li>{{ $type }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="card-footer bg-transparent p-0">
                    <div class="row m-0">
                        <button onClick="window.location.href='{{ url('/') }}'" class="btn btn-primary w-100 right">Retourner sur le Portail</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
