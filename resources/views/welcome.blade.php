@extends('layouts.app')

@section('content')

<div style="height: 100%; vertical-align: middle;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
