@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
    	<div class="col-md-12 mb-4">
    		<div class="card drop-shadow">
                <div class="card-body">
                	<h5 class="mb-4"><b>Bienvenue !</b></h5>

		    		<p>Pour des raisons pratiques, il est nécessaire de lier votre compte CAS-UTC avec votre adresse email personnel.</p>
					<p>En faisant celà, il vous est possible <b>d'accéder à votre compte</b> même si vous ne faites plus parti de l'UTC et donc garder vos <b>préférences</b> et votre <b>parcours associatif</b> par exemple.</p>
					<p>Vous pouvez bien sûr vous connecter avec l'un ou l'autre mais en ne vous connectant pas au CAS-UTC, vous n'aurez pas accès à tous les services proposés par l'UTC.</p>
				</div>
			</div>
    	</div>

        <div class="col-md-6">
            <div class="card drop-shadow">
                <div class="card-body">
                	<h5 class="mb-4"><b>Connexion</b></h5>

                    <form method="POST">
                        @csrf

                        @if (Session::has('error'))
                            <div class="alert alert-danger">
                                {{ Session::get('error')}}
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="email" class="col-md-6 col-form-label">Adresse email :</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-6 col-form-label">Mot de passe :</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-6">
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
            <div class="card drop-shadow">
                <div class="card-body">
                	<h5 class="mb-4"><b>Ajouter une connexion email/mot de passe</b></h5>

                    <form method="POST">
                        @csrf

						<div class="form-group row">
                            <label for="email" class="col-md-6 col-form-label">Adresse email :</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-6 col-form-label">Mot de passe :</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-6 col-form-label">Confirmer le mot de passe :</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-6">
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
@endsection
