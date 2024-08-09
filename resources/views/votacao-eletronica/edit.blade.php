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

<h1 class="h3 mb-3">Alteração de Votação Eletrônica</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('votacao_eletronica.update', $votacao->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data</label>
                        <input type="date" class="form-control @error('data') is-invalid @enderror" name="data" value="{{ $votacao->data }}">
                        @error('data')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Tipo de Votação</label>
                        <select name="id_tipo_votacao" class="select2 form-control @error('id_tipo_votacao') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_votacaos as $tipo_votacao)
                                <option value="{{ $tipo_votacao->id }}" {{ $tipo_votacao->id == $votacao->id_tipo_votacao ? 'selected' : '' }}>{{ $tipo_votacao->descricao }}</option>
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
                            @foreach ($legislaturas as $legislatura)
                                <option value="{{ $legislatura->id }}" {{ $legislatura->id == $votacao->id_legislatura ? 'selected' : '' }}>
                                    Início: <strong>{{ $legislatura->inicio_mandato }}</strong> -
                                    Fim: <strong>{{ $legislatura->fim_mandato }}
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
                                <option value="{{ $proposicao->id }}" {{ $proposicao->id == $votacao->id_proposicao ? 'selected' : '' }}>{{ $proposicao->titulo }}</option>
                            @endforeach
                        </select>
                        @error('id_proposicao')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                    <a href="{{ route('votacao_eletronica.index') }}" class="btn btn-light m-1">Voltar</a>
                </div>
                <br>
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
    $("#form").validate({
        rules : {
            data:{
                required:true
            },
            id_tipo_votacao:{
                required:true
            },
            id_proposicao:{
                required:true
            },
            id_legislatura:{
                required:true
            },
        },
        messages:{
            data:{
                required:"Campo obrigatório"
            },
            id_tipo_votacao:{
                required:"Campo obrigatório"
            },
            id_proposicao:{
                required:"Campo obrigatório"
            },
            id_legislatura:{
                required:"Campo obrigatório"
            },
        }
    });

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
