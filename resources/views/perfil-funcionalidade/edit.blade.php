@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- script referente ao mapa --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14="
crossorigin=""/>
<script src='https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.js'></script>
<link href='https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.css' rel='stylesheet' />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
    #map{
        background: #fff;
        border: none;
        height: 540px;
        width: 100%;

        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
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
            <strong>Alteração do Perfil</strong>
        </h2>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('perfil_funcionalidade.inativarFuncionalidade', $perfil->id) }}" id="form-inativar" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="modal-header btn-danger">
                        <h5 class="modal-title text-center" id="exampleModalLabel">
                            <strong>Desativar funcionalidade</strong>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="id_func" id="id_func" hidden>
                        <div class="form-group">
                            <label for="nome_func">Funcionalidade</label>
                            <input type="text" class="form-control" name="nome_func" id="nome_func" readonly>
                        </div>
                        <div class="form-group">
                            <label for="motivo">Motivo</label>
                            <input type="text" class="form-control" name="motivo" id="motivo" value="{{ old('motivo') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Cancelar
                        </button>
                        <button type="submit" class="button_submit btn btn-danger">Desativar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('perfil_funcionalidade.update', $perfil->id) }}" id="form-create" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <legend>Dados do Perfil</legend>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Perfil</label>
                        <input type="text" name="descricao" class="form-control" value="{{ $perfil->descricao != null ? $perfil->descricao : old('descricao') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Abrangência</label>
                        <select name="id_abrangencia" class="form-control select2">
                            @foreach ($abrangencias as $a)
                                <option value="{{ $a->id }}" {{ $a->id == $perfil->id_abrangencia ? 'selected' : '' }}>{{ $a->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br><hr><br>
                <legend>Funcionalidades a serem cadastradas</legend>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Funcionalidade</label>
                        <select name="funcionalidade_id[]" id="funcionalidade_id" class="form-control" multiple>
                            @foreach ($funcs as $func)
                                <option value="{{ $func->id }}">
                                    {{ $func->entidade->descricao }} -
                                    {{ $func->id_tipo_funcionalidade != null ? $func->tipo_funcionalidade->descricao : 'tipo de funcionalidade não informada' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br><hr><br>
                <legend>Funcionalidades já cadastradas</legend><br>
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Funcionalidade</th>
                                <th scope="col">Adicionado por</th>
                                <th scope="col">Ativo (clique no botão "Ativo" para desativar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perfil->funcionalidades as $f)
                                <tr>
                                    <td>
                                        {{ $f->funcionalidade->id_entidade != null ? $f->funcionalidade->entidade->descricao : 'entidade não informada' }} -
                                        {{ $f->funcionalidade->id_tipo_funcionalidade != null ? $f->funcionalidade->tipo_funcionalidade->descricao : 'tipo de funcionalidade não informada' }}
                                    </td>
                                    <td>
                                        <strong>{{ $f->cadastradoPorUsuario != null ? $f->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                        em <strong>{{ $f->created_at != null ? $f->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    @switch($f->ativo)
                                        @case('1')
                                            <td>
                                                <button type="button" class="inativar btn btn-success" name="{{ $f->id }}"
                                                    id="{{ $f->funcionalidade->id_entidade != null ? $f->funcionalidade->entidade->descricao : 'entidade não informada' }} - {{ $f->funcionalidade->id_tipo_funcionalidade != null ? $f->funcionalidade->tipo_funcionalidade->descricao : 'tipo de funcionalidade não informada' }}">
                                                    Ativo
                                                </button>
                                            </td>
                                            @break

                                        @default
                                            <td>
                                                <button type="button" class="btn btn-info">
                                                    Desativado
                                                    por <strong>{{ $f->inativadoPorUsuario != null ? $f->inativadoPor->pessoa->nome : 'não informado' }}</strong>
                                                    em <strong>{{ date('d/m/Y H:i:s', strtotime($f->dataInativado)) }}</strong>
                                                </button>
                                            </td>
                                            @break

                                    @endswitch
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <br>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        <a href="{{ URL::previous() }}" class="btn btn-light">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js" integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>

    $(document).ready(function() {

        $('.inativar').click(function () {
            var nome = this.id;
            var id = this.name;
            $('#nome_func').val(nome);
            $('#id_func').val(id);
            $('#ajaxModel').modal('show');
        });

        $('.select2').select2({
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                }
            },
            closeOnSelect: true,
            width: '100%',
        });

        $('#funcionalidade_id').select2({
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                }
            },
            closeOnSelect: false,
            width: '100%',
            dropdownCssClass: "bigdrop"
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
