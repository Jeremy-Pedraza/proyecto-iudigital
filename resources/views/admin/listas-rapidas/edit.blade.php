@extends('layouts/contentNavbarLayout')
@section('title', 'Listas rápidas - Editar')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Editar registro</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.listas-rapidas.update', $item->id) }}">
        @csrf @method('PUT')
        @include('sigeruta.catalogos.listas-rapidas._form', ['item' => $item])
    </form>
@endsection
