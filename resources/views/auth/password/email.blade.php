@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
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

                            <div class="col-md-7">
                                <div class="captcha mb-3">
                                    <span>{!! captcha_img("flat") !!}</span>
                                    <button type="button" onclick="refreshCaptcha()" class="btn btn-success btn-refresh"><i class="fa fa-sync-alt"></i></button>
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
                            <div class="ml-auto">
                                <button type="submit" class="btn btn-primary"  style="white-space: normal !important;">
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
      $.ajax(
          {
            type:'GET',
            url: "{{ route('login.captcha') }}",
            success: function (captcha) {
                console.log(captcha)
                $(".captcha span").html(captcha);
            },
            error : function(error){
                console.error(error);
            }
        }
      );
    };
</script>

@endsection
