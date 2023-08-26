@extends('layout.main-publico')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red,

    }
</style>
@include('errors.alerts')
@include('errors.errors')

<div class="card" style="background-color:white">

    <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Listagem de Atos</strong>
        </h2>
    </div>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="heading">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse"
                        data-target="#collapse" aria-expanded="false"
                        aria-controls="collapse">
                        Filtros
                    </button>
                </h5>
            </div>
            <div id="collapse" class="collapse show" aria-labelledby="heading"
                data-parent="#accordion">
                <div class="card-body">
                    <form action="" method="post" id="form-buscar">
                        @csrf
                        @method('POST')

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">Título</label>
                                <input name="titulo" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Assunto</label>
                                <select name="id_assunto" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($assuntos as $assunto)
                                        <option value="{{ $assunto->id }}">{{ $assunto->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Tipo de Ato</label>
                                <select name="id_tipo_ato" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($tipo_atos as $tipo_ato)
                                        <option value="{{ $tipo_ato->id }}">{{ $tipo_ato->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="ano">Ano</label>
                                <input name="ano" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="numero">Número</label>
                                <input name="numero" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="altera_dispositivo">Altera dispositivo</label>
                                <select name="altera_dispositivo" class="form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                        </div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fas fa-search-location"></i>
                                    &nbspPesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br><hr><br>

    <div id="accordion2">
        <div class="card">
            <div class="card-header" id="heading2">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse"
                        data-target="#collapse2" aria-expanded="false"
                        aria-controls="collapse">
                        Listagem
                    </button>
                </h5>
            </div>
            <div id="collapse2" class="collapse show" aria-labelledby="heading2" data-parent="#accordion2">
                <div class="card-body">
                    @if (Count($atos) == 0)
                        <div>
                            <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col">Ato</th>
                                        <th scope="col">Título</th>
                                        <th scope="col">Assunto</th>
                                        <th scope="col">Altera dispositivo</th>
                                        <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($atos as $ato)
                                        <tr>
                                            <td>
                                                @php
                                                    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                                                    date_default_timezone_set('America/Sao_Paulo');
                                                @endphp
                                                {{ $ato->id_tipo_ato != null ? $ato->tipo_ato->descricao : 'Tipo de ato não informado' }}
                                                Nº {{ $ato->numero != null ? $ato->numero : 'não informado' }},
                                                de {{ strftime('%d de %B de %Y', strtotime($ato->created_at)) }}
                                            </td>
                                            <td>{{ $ato->titulo }}</td>
                                            <td>{{ $ato->id_assunto != null ? $ato->assunto->descricao : 'não informado' }}</td>
                                            <td>{{ $ato->altera_dispositivo == 1 ? 'Sim' : 'Não' }}</td>
                                            <td>
                                                <a href="{{ route('ato.show', $ato->id) }}" class="btn btn-secondary m-1">Visualizar</a>
                                                {{-- <a href="{{ route('ato.dados_gerais.edit', $ato->id) }}" class="btn btn-warning m-1">Alterar</a>
                                                <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $ato->id }}">Excluir</button> --}}
                                            </td>
                                        </tr>

                                        {{-- <div class="modal fade" id="exampleModalExcluir{{ $usuario->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="POST" class="form_prevent_multiple_submits" action="{{ route('usuario.destroy', $usuario->id) }}">
                                                        @csrf
                                                        @method('POST')
                                                        <div class="modal-header btn-danger">
                                                            <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                                <strong style="font-size: 1.2rem">Excluir <i>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</i></strong>
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="motivo" class="form-label">Motivo</label>
                                                                <input type="text" class="form-control" name="motivo" required>
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

                                        <div class="modal fade" id="exampleModalRecadastrar{{ $usuario->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelRecadastrar"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="POST" class="form_prevent_multiple_submits" action="{{ route('usuario.restore', $usuario->id) }}">
                                                        @csrf
                                                        @method('POST')
                                                        <div class="modal-header btn-primary">
                                                            <h5 class="modal-title text-center" id="exampleModalLabelRecadastrar">
                                                                <strong style="font-size: 1.2rem">Recadastrar <i>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</i></strong>
                                                            </h5>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                                                            </button>
                                                            <button type="submit" class="button_submit btn btn-primary">Recadastrar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div> --}}
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <div class="card-footer">
        <a href="{{ route('ato.create') }}" class="btn btn-primary">Cadastrar Ato</a>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>

    $('.cpf').mask('000.000.000-00');

    $(document).ready(function() {

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
