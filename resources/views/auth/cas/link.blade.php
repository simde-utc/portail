@extends('layouts.app')

@section('content')
	<div style="position: absolute; width: 100%; top: 50%; transform: translateY(-50%); display: flex; justify-content: center;">
		<div>
			Pour des raisons pratiques, il est nécessaire de lier votre compte CAS-UTC avec votre adresse email personnel.<br />
			<br />
			En faisant celà, il vous est possible d'accéder à votre compte même si vous ne faites plus parti de l'UTC et donc garder vos préférences et votre parcours associatif par exemple.<br />
			<br />
			Vous pouvez bien sûr vous connecter avec l'un ou l'autre mais en ne vous connectant pas au CAS-UTC, vous n'aurez pas accès à tous les services proposés par l'UTC.<br />
			<br />

			<div class="container">
			    <div class="row justify-content-center">
			        <div class="col-md-6 mb-4">
			            <div class="card card-default">
			                <div class="card-header">Se connecter avec son ancien compte</div>

			                <div class="card-body">
			                    <form method="POST">
			                        @csrf

			                        @if (Session::has('error'))
			                            <div class="alert alert-danger">
			                                {{ Session::get('error')}}
			                            </div>
			                        @endif

			                        <div class="form-group row">
			                            <label for="email" class="col-sm-4 col-form-label text-md-right">Adresse email</label>

			                            <div class="col-md-6">
			                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
			                            </div>
			                        </div>

			                        <div class="form-group row">
			                            <label for="password" class="col-md-4 col-form-label text-md-right">Mot de passe</label>

			                            <div class="col-md-6">
			                                <input id="password" type="password" class="form-control" name="password" required>
			                            </div>
			                        </div>

			                        <div class="form-group row mb-0">
			                            <div class="col-md-8 offset-md-4">
			                                <button type="submit" class="btn btn-primary">
			                                    Lier mon ancien compte
			                                </button>
			                            </div>
			                        </div>
			                    </form>
			                </div>
			            </div>
			        </div>

			        <div class="col-md-6">
			            <div class="card card-default">
			                <div class="card-header">Ajouter une connexion email/mot de passe</div>

			                <div class="card-body">
			                    <form method="POST">
			                        @csrf

									<div class="form-group row">
			                            <label for="email" class="col-md-4 col-form-label text-md-right">Adresse email</label>

			                            <div class="col-md-6">
			                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

			                                @if ($errors->has('email'))
			                                    <span class="invalid-feedback">
			                                        <strong>{{ $errors->first('email') }}</strong>
			                                    </span>
			                                @endif
			                            </div>
			                        </div>

			                        <div class="form-group row">
			                            <label for="password" class="col-md-4 col-form-label text-md-right">Mot de passe</label>

			                            <div class="col-md-6">
			                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

			                                @if ($errors->has('password'))
			                                    <span class="invalid-feedback">
			                                        <strong>{{ $errors->first('password') }}</strong>
			                                    </span>
			                                @endif
			                            </div>
			                        </div>

			                        <div class="form-group row">
			                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirmer le mot de passe</label>

			                            <div class="col-md-6">
			                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
			                            </div>
			                        </div>

			                        <div class="form-group row mb-0">
			                            <div class="col-md-8 offset-md-4">
			                                <button type="submit" class="btn btn-primary">
			                                    Créer mon compte lié
			                                </button>
			                            </div>
			                        </div>
			                    </form>
			                </div>
			            </div>
			        </div>
				</div>
			</div>
		</div>
	</div>
@endsection
