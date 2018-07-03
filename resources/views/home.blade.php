@extends('layouts.app')

@section('content')
<div style="height: 100%; vertical-align: middle;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="my-4"><b>Dashboard</b></h4>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
			<passport-clients></passport-clients>
			<passport-authorized-clients></passport-authorized-clients>
			<passport-personal-access-tokens></passport-personal-access-tokens>
        </div>
    </div>
</div>

@endsection

@section('script')

<script src="{{ asset('js/app.js') }}"></script>

@endsection
