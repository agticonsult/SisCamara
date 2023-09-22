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
            <strong>Alteração de Pleito Eleitoral</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('processo_legislativo.pleito_eleitoral.update', $pleito_eleitoral->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Ano do Pleito Eleitoral</label>
                        <input type="text" class="ano form-control" name="ano_pleito" value="{{ $pleito_eleitoral->ano_pleito }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">Pleito Especial</label>
                        <select name="pleitoEspecial" class="form-control">
                            @if ($pleito_eleitoral->pleitoEspecial == 1)
                                <option value="0">Não</option>
                                <option value="1" selected>Sim</option>
                            @else
                                <option value="0" selected>Não</option>
                                <option value="1">Sim</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Início do mandato</label>
                        <input type="text" class="ano form-control" name="inicio_mandato" value="{{ $pleito_eleitoral->inicio_mandato }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Fim do mandato</label>
                        <input type="text" class="ano form-control" name="fim_mandato" value="{{ $pleito_eleitoral->fim_mandato }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data do primeiro turno</label>
                        <input type="date" class="form-control" name="dataPrimeiroTurno" value="{{ $pleito_eleitoral->dataPrimeiroTurno }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data do segundo turno</label>
                        <input type="date" class="form-control" name="dataSegundoTurno" value="{{ $pleito_eleitoral->dataSegundoTurno }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">Cargos eletivos</label>
                        <select name="id_cargo_eletivo[]" class="select2 form-control" multiple>
                            @foreach ($cargo_eletivos as $cargo_eletivo)
                                <option value="{{ $cargo_eletivo->id }}">{{ $cargo_eletivo->descricao }}</option>
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

    <div class="card-body">
        <div class="col-md-12">
            <hr><br>
            <h5>Listagem de Cargos Eletivos do Pleito</h5>
            <br>
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Cargo Eletivo</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Status <br>(para desativar este perfil deste usuário, clique no botão "Ativo")</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pleito_eleitoral->cargos_eletivos() as $pleito_cargo)
                            <tr>
                                <td>{{ $pleito_cargo->cargo_eletivo->descricao }}</td>
                                <td>
                                    <strong>{{ $pleito_cargo->cargo_eletivo->cadastradoPorUsuario != null ? $pleito_cargo->cargo_eletivo->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $pleito_cargo->cargo_eletivo->created_at != null ? $pleito_cargo->cargo_eletivo->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    @switch($pleito_cargo->cargo_eletivo->ativo)
                                        @case(1)
                                            <button type="button" class="desativar btn btn-success" name="{{ $pleito_cargo->cargo_eletivo->id }}" id="{{ $pleito_cargo->cargo_eletivo->descricao }}">
                                                Ativo
                                            </button>
                                            @break
                                        @default
                                            <button type="button" class="btn btn-info">
                                                Desativado
                                                por <strong>{{ $pleito_cargo->cargo_eletivo->inativadoPorUsuario != null ? $pleito_cargo->cargo_eletivo->inativadoPor->pessoa->nomeCompleto : 'não informado' }}</strong>
                                                em <strong>{{ date('d/m/Y H:i:s', strtotime($pleito_cargo->cargo_eletivo->dataInativado)) }}</strong>
                                            </button>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            inicio_mandato:{
                required:true
            },
            fim_mandato:{
                required:true
            },
            dataPrimeiroTurno:{
                required:true
            },
            dataSegundoTurno:{
                required:true
            }
        },
        messages:{
            ano_pleito:{
                required:"Campo obrigatório"
            },
            inicio_mandato:{
                required:"Campo obrigatório"
            },
            fim_mandato:{
                required:"Campo obrigatório"
            },
            dataPrimeiroTurno:{
                required:"Campo obrigatório"
            },
            dataSegundoTurno:{
                required:"Campo obrigatório"
            }
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

        $('#datatables-reponsive').dataTable({
            "oLanguage": {
                "sLengthMenu": "Mostrar _MENU_ registros por página",
                "sZeroRecords": "Nenhum registro encontrado",
                "sInfo": "Mostrando _START_ / _END_ de _TOTAL_ registro(s)",
                "sInfoEmpty": "Mostrando 0 / 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros)",
                "sSearch": "Pesquisar: ",
                "oPaginate": {
                    "sFirst": "Início",
                    "sPrevious": "Anterior",
                    "sNext": "Próximo",
                    "sLast": "Último"
                }
            },
        });

    });

</script>

@endsection
