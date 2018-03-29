@extends('layouts.app')

@section('content')
<div class="passport-authorize">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        Demande d'accès à vos données
						<?php
                        // TODO : CRAAAAAAAAAAAAAAAAAAAAAAAAAADE
							$asso_id = $client->asso_id;
							$asso = \App\Models\Asso::find($asso_id);
						?>
						@if ($asso_id !== null && $asso !== null)
							par l'association <strong>{{ $asso->name }}</strong>
						@endif
                    </div>
                    <div class="card-body">
                        <!-- Introduction -->
                        <p><strong>{{ $client->name }}</strong> souhaite accéder à vos données.</p>

                        <!-- Scope List -->
                        @if (count($scopes) > 0)
                            <div class="scopes">
                                <p><strong>Cette application demande les accès suivants:</strong></p>

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

                        <div class="buttons">
                            <!-- Authorize Button -->
                            <form method="post" action="/oauth/authorize">
                                {{ csrf_field() }}

                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <button type="submit" class="btn btn-success btn-approve">Autoriser</button>
                            </form>

                            <!-- Cancel Button -->
                            <form method="post" action="/oauth/authorize">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <button class="btn btn-danger">Refuser</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection('content')
