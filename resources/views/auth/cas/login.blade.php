@extends('layouts.app')

@section('content')
	<iframe onLoad="url = this.contentWindow.location.href; if (url !== '{{ config('portail.cas.url').'login?service='.route('login.process', ['provider' => 'cas']) }}') { top.window.location.href = this.src; }" src="{{ config('portail.cas.url').'login?service='.route('login.process', ['provider' => 'cas']) }}" style="position: absolute; height:100%; width:100%" frameborder="0" height="100%" width="100%"></iframe>
@endsection
