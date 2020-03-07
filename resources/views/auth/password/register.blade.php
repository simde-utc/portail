@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card drop-shadow">
            <div class="card-body">
                <h4 class="mb-4"><b>Inscription</b></h4>

                <form method="POST" action="{{ route('register.process', ['provider' => $provider, 'redirect' => $redirect]) }}">
                    @csrf

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label">Prénom :</label>

                        <div class="col-md-6">
                            <input id="firstname" type="text" class="form-control {{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{ old('firstname') }}" required>

                            @if ($errors->has('firstname'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('firstname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label">Nom :</label>

                        <div class="col-md-6">
                            <input id="lastname" type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{ old('lastname') }}" required>

                            @if ($errors->has('lastname'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('lastname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label">Adresse email :</label>

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
                        <label for="birthdate" class="col-md-4 col-form-label">Date de naissance :</label>

                        <div class="col-md-6">
                            <input id="birthdate" type="date" class="form-control{{ $errors->has('birthdate') ? ' is-invalid' : '' }}" name="birthdate" max="{{ \Carbon\Carbon::now()->subYears(16)->toDateString() }}" value="{{ old('birthdate') }}" required>

                            @if ($errors->has('birthdate'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('birthdate') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label">Mot de passe :</label>

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
                        <label for="password-confirm" class="col-md-4 col-form-label">Confirmer le mot de passe :</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('captcha') ? ' has-error' : '' }} row">
                        <label for="captcha" class="col-md-4 col-form-label">Résultat du Captcha :</label>

                        <div class="col-md-6">
                            <div class="captcha mb-2">
                                <span>{!! captcha_img("flat") !!}</span>
                                <button type="button" onclick="refreshCaptcha()" class="btn btn-success btn-sm"><i class="fas fa-sync-alt"></i></button>
                            </div>

                            <input id="captcha" type="text" class="form-control {{ $errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha" required>
                            @if ($errors->has('captcha'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('captcha') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary px-4">
                                Inscription
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<script type="text/javascript">
    var refreshCaptcha = function () {
        $.ajax({
            type:'GET',
            url: "{{ route('login.captcha') }}",
            success: function (captcha) {
                $(".captcha span").html(captcha);
            },
            error: function(error){
                console.error(error);
            }
        });
    };
</script>

@endsection
