@extends('layouts.app')

@section('content')
<div style="height: 100%; vertical-align: middle;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-default">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    Bonjour, {{ Auth::user()->lastname }} {{ Auth::user()->firstname }} !
                </div>
            </div>
			<passport-clients></passport-clients>
			<passport-authorized-clients></passport-authorized-clients>
			<passport-personal-access-tokens></passport-personal-access-tokens>
        </div>
    </div>
</div>

@endsection
