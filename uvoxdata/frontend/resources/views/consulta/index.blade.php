@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @include('consulta.partials.document-upload')
    @include('consulta.partials.chat-input')

    <div id="respuesta-container" class="hidden">
        @include('consulta.partials.response-card')
    </div>

    <div id="aclaracion-container" class="hidden">
        @include('consulta.partials.clarification')
    </div>
</div>
@endsection
