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
@include('errors.errors')

<div class="card" style="background-color:white">

    <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Listagem de Agentes Políticos</strong>
        </h2>
    </div>

    <div class="card-body">
        @if (Count($agente_politicos) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Mandato</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agente_politicos as $agente)
                            <tr>
                                <td><strong>{{ $agente->id_user != null ? $agente->usuario->pessoa->nomeCompleto : 'não informado' }}</strong></td>
                                <td>
                                    Início: <strong>{{ $agente->dataInicioMandato != null ? date('d/m/Y', strtotime($agente->dataInicioMandato)) : 'não informado' }} </strong><br>
                                    Fim: <strong>{{ $agente->dataFimMandato != null ? date('d/m/Y', strtotime($agente->dataFimMandato)) : 'não informado' }} </strong>
                                </td>
                                <td><strong>{{ $agente->cargo_eletivo->descricao }}</strong></td>
                                <td>
                                    <strong>{{ $agente->cadastradoPorUsuario != null ? $agente->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $agente->created_at != null ? $agente->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    {{-- <a href="{{ route('agente_politico.edit', $agente->id) }}" class="btn btn-warning m-1">Alterar</a> --}}
                                    <a href="{{ route('agente_politico.edit', $agente->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                    {{-- <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $agente->id }}">Excluir</button> --}}
                                </td>
                                {{-- $table->date('')->nullable();
                                $table->date('')->nullable();
                                $table->integer('id_cargo_eletivo')->unsigned()->nullable();
                                $table->integer('id_pleito_eleitoral')->unsigned()->nullable();
                                $table->uuid('id_user')->nullable(); --}}
                                {{-- <td><strong>{{ $agente->ano_pleito != null ? $agente->ano_pleito : 'não informado' }}</strong></td>
                                <td>Início: <strong>{{ $agente->inicio_mandato }}</strong> - Fim: <strong>{{ $agente->fim_mandato }}</strong></td>
                                <td>
                                    Primeiro turno: <strong>{{ $agente->dataPrimeiroTurno != null ? date('d/m/Y', strtotime($agente->dataPrimeiroTurno)) : 'não informado' }} </strong><br>
                                    Segundo turno: <strong>{{ $agente->dataSegundoTurno != null ? date('d/m/Y', strtotime($agente->dataSegundoTurno)) : 'não informado' }} </strong>
                                </td>
                                <td>
                                    @if (Count($agente->cargos_eletivos_ativos()) != 0)
                                        <ol>
                                            @foreach ($agente->cargos_eletivos_ativos() as $agente)
                                                <li>{{ $agente->cargo_eletivo->descricao }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        Nenhum cargo eletivo cadastrado
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $agente->cadastradoPorUsuario != null ? $agente->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $agente->created_at != null ? $agente->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('agente_politico.edit', $agente->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $agente->id }}">Excluir</button>
                                </td> --}}
                                {{-- Início: {{ date('d/m/Y', strtotime($agente->inicio_mandato)) }} - Fim: {{ date('d/m/Y', strtotime($agente->fim_mandato)) }} --}}
                                {{-- <td>{{ $agente->id_tipo_pleito != null ? $agente->tipo_pleito->descricao : 'não informado' }}</td>
                                <td>
                                    <strong>{{ $agente->cadastradoPorUsuario != null ? $agente->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $agente->created_at != null ? $agente->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('processo_legislativo.pleito.edit', $agente->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $agente->id }}">Excluir</button>
                                </td> --}}
                            </tr>

                            {{-- <div class="modal fade" id="exampleModalExcluir{{ $agente->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('agente_politico.destroy', $agente->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    <strong style="font-size: 1.2rem">Excluir Pleito de <i>{{ $agente->ano_pleito != null ? $agente->ano_pleito : 'não informado' }}</i></strong>
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
                            </div> --}}

                            {{-- <div class="modal fade" id="exampleModalRecadastrar{{ $usuario->id }}"
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

    <div class="card-footer">
        <a href="{{ route('agente_politico.create') }}" class="btn btn-primary">Cadastrar Agente Político</a>
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
