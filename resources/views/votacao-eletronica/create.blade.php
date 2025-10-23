@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('sweetalert::alert')

@if (!isset($proposicoes[0]))
    <div class="alert alert-warning alert-dismissible" role="alert">
        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
        <div class="alert-message">
            <strong>Sem PROPOSIÇÃO cadastrado!</strong>
            <a href="{{ route('proposicao.create') }}">Clique aqui para cadastrar</a>
        </div>
    </div>
@endif

<h1 class="h3 mb-3">Cadastro de Votação Eletrônica</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('votacao_eletronica.store') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data</label>
                        <input type="date" class="form-control @error('data') is-invalid @enderror" name="data">
                        @error('data')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Tipo de Votação</label>
                        <select name="id_tipo_votacao" class="select2 form-control @error('id_tipo_votacao') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_votacaos as $tipo_votacao)
                                <option value="{{ $tipo_votacao->id }}">{{ $tipo_votacao->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_votacao')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Legislatura</label>
                        <select name="id_legislatura" class="select2 form-control @error('id_legislatura') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($legislaturas as $legislatura)
                                <option value="{{ $legislatura->id }}">
                                    Início: <strong>{{ $legislatura->inicio_mandato }}</strong> -
                                    Fim: <strong>{{ $legislatura->fim_mandato }}</strong>
                                </option>
                            @endforeach
                        </select>
                        @error('id_legislatura')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Proposição</label>
                        <select name="id_proposicao" class="select2 form-control @error('id_proposicao') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($proposicaos as $proposicao)
                                <option value="{{ $proposicao->id }}">{{ $proposicao->titulo }}</option>
                            @endforeach
                        </select>
                        @error('id_proposicao')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                        <a href="{{ route('votacao_eletronica.index') }}" class="btn btn-light m-1">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
    $(document).ready(function() {

        $('.select2').select2({
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                }
            },
            closeOnSelect: true,
            width: '100%',
        });

    });

</script>

@endsection
