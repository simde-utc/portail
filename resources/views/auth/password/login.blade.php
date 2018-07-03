@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card drop-shadow">
                <div class="card-body">
                    <h4 class="mb-4"><b>Connexion</b></h4>

                    <form method="POST" action="{{ route('login.process', ['provider' => $provider]) }}">
                        @csrf

                        @if (Session::has('error'))
                            <div class="alert alert-danger">
                                {{ Session::get('error')}}
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="email" class="col-sm-4 col-form-label">Adresse email :</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label">Mot de passe :</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <div class="col-md-6 offset-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Rester connecté
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    Connexion
                                </button>
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Mot de passe oublié
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
