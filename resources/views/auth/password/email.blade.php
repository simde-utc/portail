@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-default">
                <div class="card-header">Réinitialisation du mot de passe</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
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

                        <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }} row">
                            <label for="captcha" class="col-md-4 col-form-label text-md-right">Captcha</label>

                            <div class="col-md-6">
                                <div class="captcha">
                                    <span>{!! captcha_img() !!}</span>
                                    <button type="button" onclick="refreshCaptcha()" class="btn btn-success btn-refresh"><i class="fa fa-refresh"></i></button>
                                </div>

                                <input id="captcha" type="text" class="form-control{{ $errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha" required>
                                @if ($errors->has('captcha'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('captcha') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Envoyer un email pour réinitialiser le mot de passe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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
         success: function (data) {
            $(".captcha span").html(data.captcha);
         }
      });
    };
</script>

@endsection
