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
<div class="card">

    <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Alteração de Ato</strong>
        </h2>
    </div>

    <br>
    <div class="row">
        <div class="col-md-12">

            <ul class="nav nav-pills nav-justified">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ato.editDadosGerais', $ato->id) || Route::current()->uri == 'ato/edit/dados-gerais/{id}' ? 'active' : null }}" href="{{ route('ato.editDadosGerais', $ato->id) }}">Dados Gerais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ato.editCorpoTexto', $ato->id) || Route::current()->uri == 'ato/edit/corpo-do-texto/{id}' ? 'active' : null }}" href="{{ route('ato.editCorpoTexto', $ato->id) }}">Corpo do texto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ato.editAnexos', $ato->id) || Route::current()->uri == 'ato/edit/anexos/{id}' ? 'active' : null }}" href="{{ route('ato.editAnexos', $ato->id) }}">Anexos</a>
                </li>
               {{--
                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('gerenciamento.processo.cliente.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/cliente/{id}' ? 'active' : null }}"
                        href="{{ route('gerenciamento.processo.cliente.index', $processo->id) }}">Clientes</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('gerenciamento.processo.grupo.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/grupo/{id}' ? 'active' : null }}"
                        href="{{ route('gerenciamento.processo.grupo.index', $processo->id) }}">Grupos</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('gerenciamento.processo.anexo.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/anexo/{id}' ? 'active' : null }}"
                        href="{{ route('gerenciamento.processo.anexo.index', $processo->id) }}">Anexos</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('gerenciamento.processo.encaminhamento.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/encaminhamento/{id}' ? 'active' : null }}"
                        href="{{ route('gerenciamento.processo.encaminhamento.index', $processo->id) }}">Encaminhamentos</a>
                </li> --}}
            </ul>
        </div>
        <div class="col-lg-12 tab-content">

            @if ((request()->routeIs('ato.editDadosGerais', $ato->id) || (Route::current()->uri =='ato/edit/dados-gerais/{id}')))
                <div class="tab-pane {{ request()->routeIs('ato.editDadosGerais', $ato->id) || Route::current()->uri == 'ato/edit/dados-gerais/{id}' ? 'active' : null }}"
                    id="{{ route('ato.editDadosGerais', $ato->id) }}">
                    @include('ato.dadosGerais')
                </div>
            @endif

            @if ((request()->routeIs('ato.editCorpoTexto', $ato->id) || (Route::current()->uri =='ato/edit/corpo-do-texto/{id}')))
                <div class="tab-pane {{ request()->routeIs('ato.editCorpoTexto', $ato->id) || Route::current()->uri == 'ato/edit/corpo-do-texto/{id}' ? 'active' : null }}"
                    id="{{ route('ato.editCorpoTexto', $ato->id) }}">
                    @include('ato.corpoTexto')
                </div>
            @endif

            @if ((request()->routeIs('ato.editAnexos', $ato->id) || (Route::current()->uri =='ato/edit/anexos/{id}')))
                <div class="tab-pane {{ request()->routeIs('ato.editAnexos', $ato->id) || Route::current()->uri == 'ato/edit/anexos/{id}' ? 'active' : null }}"
                    id="{{ route('ato.editAnexos', $ato->id) }}">
                    @include('ato.editAnexos')
                </div>
            @endif

            {{--

            @if ((request()->routeIs('gerenciamento.processo.cliente.index', $processo->id) || (Route::current()->uri =='gerenciamento/processo/cliente/index/{id}')))
                <div class="tab-pane {{ request()->routeIs('gerenciamento.processo.cliente.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/cliente/index/{id}' ? 'active' : null }}"
                    id="{{ route('gerenciamento.processo.cliente.index', $processo->id) }}">
                    @include('gerenciamento.processo.cliente')
                </div>
            @endif

            @if ((request()->routeIs('gerenciamento.processo.grupo.index', $processo->id) || (Route::current()->uri =='gerenciamento/processo/grupo/index/{id}')))
                <div class="tab-pane {{ request()->routeIs('gerenciamento.processo.grupo.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/grupo/index/{id}' ? 'active' : null }}"
                    id="{{ route('gerenciamento.processo.grupo.index', $processo->id) }}">
                    @include('gerenciamento.processo.grupo')
                </div>
            @endif

            @if ((request()->routeIs('gerenciamento.processo.anexo.index', $processo->id) || (Route::current()->uri =='gerenciamento/processo/anexo/index/{id}')))
                <div class="tab-pane {{ request()->routeIs('gerenciamento.processo.anexo.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/anexo/index/{id}' ? 'active' : null }}"
                    id="{{ route('gerenciamento.processo.anexo.index', $processo->id) }}">
                    @include('gerenciamento.processo.anexo')
                </div>
            @endif

            @if ((request()->routeIs('gerenciamento.processo.encaminhamento.index', $processo->id) || (Route::current()->uri =='gerenciamento/processo/encaminhamento/index/{id}')))
                <div class="tab-pane {{ request()->routeIs('gerenciamento.processo.encaminhamento.index', $processo->id) || Route::current()->uri == 'gerenciamento/processo/encaminhamento/index/{id}' ? 'active' : null }}"
                    id="{{ route('gerenciamento.processo.grupo.index', $processo->id) }}">
                    @include('gerenciamento.processo.encaminhamento')
                </div>
            @endif --}}

        </div>
    </div>

    <div class="card-footer">
        <div class="col-md-12">
            <a href="{{ route('ato.index') }}" class="btn btn-light">Voltar</a>
        </div>
    </div>

</div>

@stop
