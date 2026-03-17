@extends('ieducar-pulse::layouts.fullscreen')

@section('content')
<iframe src="{{ url(config('pulse.path', 'pulse')) }}" title="Monitoramento i-Educar"></iframe>
@endsection
