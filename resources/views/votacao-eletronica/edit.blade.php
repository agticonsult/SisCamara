@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAUgxBPrGkKz6xNwW6Z1rJh26AqR8ct37A"></script>
<script src="{{ asset('js/gmaps.js') }}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')
@include('errors.errors')

<div class="card" style="background-color:white">

    <div class="card-header">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Alteração de Votação Eletrônica</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('votacao_eletronica.update', $votacao->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data</label>
                        <input type="date" class="form-control" name="data" value="{{ $votacao->data }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Tipo de Votação</label>
                        <select name="id_tipo_votacao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_votacaos as $tipo_votacao)
                                <option value="{{ $tipo_votacao->id }}" {{ $tipo_votacao->id == $votacao->id_tipo_votacao ? 'selected' : '' }}>{{ $tipo_votacao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Proposição</label>
                        <select name="id_proposicao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($proposicaos as $proposicao)
                                <option value="{{ $proposicao->id }}" {{ $proposicao->id == $votacao->id_proposicao ? 'selected' : '' }}>{{ $proposicao->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
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
