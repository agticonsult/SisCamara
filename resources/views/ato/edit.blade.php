@extends('layout.main')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.errors')
@include('errors.alerts')

{{-- <div class="container box box-primary" style="padding: 3rem;"> --}}
{{-- <div class="container box box-primary"> --}}
<h1 class="h3 mb-3">Alteração de Ato</h1>
<div class="card">

    {{-- <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Alteração de Ato</strong>
        </h2>
    </div> --}}

    <br>
    <div class="row">
        <div class="col-md-12">

            <ul class="nav nav-pills nav-justified">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ato.dados_gerais.edit', $ato->id) || Route::current()->uri == 'ato/dados-gerais/edit/{id}' ? 'active' : null }}" href="{{ route('ato.dados_gerais.edit', $ato->id) }}">Dados Gerais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ato.edit.corpo_texto', $ato->id) || Route::current()->uri == 'ato/corpo-do-texto/edit/{id}' ? 'active' : null }}" href="{{ route('ato.corpo_texto.edit', $ato->id) }}">Corpo do texto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ato.anexos.edit', $ato->id) || Route::current()->uri == 'ato/anexos/edit/{id}' ? 'active' : null }}" href="{{ route('ato.anexos.edit', $ato->id) }}">Anexos</a>
                </li>
            </ul>
        </div>
        <div class="col-lg-12 tab-content">

            @if ((request()->routeIs('ato.dados_gerais.edit', $ato->id) || (Route::current()->uri =='ato/dados-gerais/edit/{id}')))
                <div class="tab-pane {{ request()->routeIs('ato.dados_gerais.edit', $ato->id) || Route::current()->uri == 'ato/dados-gerais/edit/{id}' ? 'active' : null }}"
                    id="{{ route('ato.dados_gerais.edit', $ato->id) }}">
                    @include('ato.dadosGerais')
                </div>
            @endif

            @if ((request()->routeIs('ato.corpo_texto.edit', $ato->id) || (Route::current()->uri =='ato/corpo-do-texto/edit/{id}')))
                <div class="tab-pane {{ request()->routeIs('ato.corpo_texto.edit', $ato->id) || Route::current()->uri == 'ato/corpo-do-texto/edit/{id}' ? 'active' : null }}"
                    id="{{ route('ato.corpo_texto.edit', $ato->id) }}">
                    @include('ato.corpoTexto')
                </div>
            @endif

            @if ((request()->routeIs('ato.anexos.edit', $ato->id) || (Route::current()->uri =='ato/anexos/edit/{id}')))
                <div class="tab-pane {{ request()->routeIs('ato.anexos.edit', $ato->id) || Route::current()->uri == 'ato/anexos/edit/{id}' ? 'active' : null }}"
                    id="{{ route('ato.anexos.edit', $ato->id) }}">
                    @include('ato.editAnexos')
                </div>
            @endif
        </div>
    </div>

    <div class="card-footer">
        <div class="col-md-12">
            <a href="{{ route('ato.index') }}" class="btn btn-light">Voltar</a>
        </div>
    </div>
</div>

@stop
