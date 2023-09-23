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
            <strong>Cadastro de Pleito Eleitoral</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('processo_legislativo.pleito_eleitoral.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Ano do Pleito Eleitoral</label>
                        <input type="text" class="ano form-control" name="ano_pleito">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">Pleito Especial</label>
                        <select name="pleitoEspecial" class="form-control">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data do primeiro turno</label>
                        <input type="date" class="form-control" name="dataPrimeiroTurno">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data do segundo turno</label>
                        <input type="date" class="form-control" name="dataSegundoTurno">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Legislatura</label>
                        <select name="id_legislatura" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($legislaturas as $legislatura)
                                <option value="{{ $legislatura->id }}">
                                    Início: <strong>{{ $legislatura->inicio_mandato }}</strong> -
                                    Fim: <strong>{{ $legislatura->fim_mandato }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Cargos eletivos</label>
                        <select name="id_cargo_eletivo[]" class="select2 form-control" multiple>
                            @foreach ($cargo_eletivos as $cargo_eletivo)
                                <option value="{{ $cargo_eletivo->id }}">{{ $cargo_eletivo->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                    <a href="{{ route('processo_legislativo.pleito_eleitoral.index') }}" class="btn btn-light m-1">Voltar</a>
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
    $('.ano').mask('0000');

    $("#form").validate({
        rules : {
            ano_pleito:{
                required:true
            },
            id_legislatura:{
                required:true
            },
            dataPrimeiroTurno:{
                required:true
            },
            dataSegundoTurno:{
                required:true
            },
            "id_cargo_eletivo[]":{
                required:true
            },
        },
        messages:{
            ano_pleito:{
                required:"Campo obrigatório"
            },
            id_legislatura:{
                required:"Campo obrigatório"
            },
            dataPrimeiroTurno:{
                required:"Campo obrigatório"
            },
            dataSegundoTurno:{
                required:"Campo obrigatório"
            },
            "id_cargo_eletivo[]":{
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
