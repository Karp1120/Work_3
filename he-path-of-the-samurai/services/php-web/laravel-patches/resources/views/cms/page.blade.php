@extends('layouts.app')

@section('content')
<div class="container my-3">
    <h3 class="mb-3">{{ htmlspecialchars($title, ENT_QUOTES, 'UTF-8') }}</h3>
    {{-- БЕЗОПАСНО: экранированное содержимое из БД --}}
    {!! htmlspecialchars_decode($html, ENT_QUOTES) !!}
</div>
@endsection