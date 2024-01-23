@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')

<h1 class="h3 mb-3">Alteração Departamento</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('configuracao.departamento.update', $departamento->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Nome</label>
                            <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao" value="{{ $departamento->descricao != null ? $departamento->descricao : old('descricao') }}" placeholder="Nome do departamento">
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="id_coordenador">Coordenador</label>
                            <select name="id_coordenador" class="form-control @error('id_coordenador') is-invalid @enderror select2">
                                <option value="" selected disabled>-- Selecione --</option>
                                @foreach ($usuarios as $usuario)
                                    @php
                                        $temCoordenador = 0;
                                        if ($departamento->id_coordenador == $usuario->id) {
                                            $temCoordenador = 1;
                                        }
                                    @endphp
                                    @if ($temCoordenador == 1)
                                        <option value="{{ $usuario->id }}" selected>{{ $usuario->pessoa->nome }}</option>
                                    @else
                                        <option value="{{ $usuario->id }}">{{ $usuario->pessoa->nome }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('id_coordenador')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Usuário</label>
                            <select name="id_user[]" class="form-control @error('id_user') is-invalid @enderror select2" multiple>
                                @foreach ($usuarios as $usuario)
                                    @php
                                        $temUsuario = 0;
                                        foreach ($departamento->usuarios as $usuarioDepartamento) {
                                            if ($usuarioDepartamento->id == $usuario->id){
                                                $temUsuario = 1;
                                            }
                                        }
                                    @endphp
                                    @if ($temUsuario == 1)
                                        <option value="{{ $usuario->id }}" selected>{{ $usuario->pessoa->nome }}</option>
                                    @else
                                        <option value="{{ $usuario->id }}">{{ $usuario->pessoa->nome }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            <a href="{{ route('configuracao.departamento.index') }}" class="btn btn-light">Voltar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <hr><br>
            <h3>Usuários vinculados ao departamento</h3>
            <br>
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">CPF</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Desvincular</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departamento->usuarios as $usuarioDepartamento)
                            <tr>
                                <td >
                                    {{ $usuarioDepartamento->pessoa->nome }}
                                </td>
                                <td class="cpf">
                                    {{ $usuarioDepartamento->cpf }}
                                </td>
                                <td>
                                    {{ $usuarioDepartamento->email }}
                                </td>
                                <td>
                                    <button type="button" class="desativar btn btn-danger" name="{{ $usuarioDepartamento->id_user }}" data-toggle="modal" data-target="#exampleModalExcluir{{ $usuarioDepartamento->id }}" style="width: 100%">
                                        <i class="align-middle me-2 fas fa-fw fa-trash"></i>
                                    </button>
                                    {{-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalExcluir{{ $usuarioDepartamento->id }}"><i class="align-middle me-2 fas fa-fw fa-trash"></i></button> --}}
                                </td>
                            </tr>
                            <div class="modal fade" id="exampleModalExcluir{{ $usuarioDepartamento->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('configuracao.departamento.desvincularUsuario', $usuarioDepartamento->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    Desvincular do departamento: <strong>{{ $usuarioDepartamento->pessoa->nome }} - {{ $usuarioDepartamento->email }}</strong>?
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="motivo" class="form-label">Motivo</label>
                                                    <input type="text" class="form-control" name="motivo">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                                                </button>
                                                <button type="submit" class="button_submit btn btn-danger">Excluir</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script>
    $('.cpf').mask('000.000.000-00');
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
