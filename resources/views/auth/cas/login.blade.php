@extends('layouts.app')

@section('content')
	<iframe onLoad="url = this.contentWindow.location.href; if (url !== '{{ config('portail.cas.url').'login?service='.route('login.process', ['provider' => 'cas', 'redirect' => $redirect]) }}') { top.window.location.href = '{{ $redirect }}'; }" src="{{ config('portail.cas.url').'login?service='.route('login.process', ['provider' => 'cas', 'redirect' => $redirect]) }}" style="position: absolute; height:100%; width:100%; margin-top:-20px" frameborder="0" height="100%" width="100%"></iframe>
@endsection
