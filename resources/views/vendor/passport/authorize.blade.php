@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-fused-buttons drop-shadow mb-4">
                <div class="card-body">
                    <h5 class="mb-3">
                        Demande d'accès à vos données par l'association <strong>{{ \App\Models\Asso::find($client->asso_id)->name }}</strong>
                    </h5>

                    <!-- Introduction -->
                    <p><b>{{ $client->name }}</b> requiert votre permission pour accéder à votre compte.</p>

                    <!-- Scope List -->
                    @if (count($scopes) > 0)
                        <div>
                                <p><b>Cette application pourra :</b></p>

                                <table>
                                    @foreach (\Scopes::getByCategories(explode(' ', $request->input('scope'))) as $categorie)
										<tr>
											<td style="width: 50px; text-align: center"><i class="fa fa-{{ $categorie['icon'] }} fa-2x text-center"/></i></td>
											<td>{{ $categorie['description'] }}</td>
										</tr>
										<tr><td></td><td>
											<ul>
												@foreach ($categorie['scopes'] as $description)
													<li>{{ $description }}</li>
												@endforeach
											</ul>
										</tr></td>
                                    @endforeach
                                </table>
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
                                <button type="submit" class="btn btn-primary text-danger w-100 left">Refuser</button>
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
