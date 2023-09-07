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
            <strong>Listagem de Vereadores</strong>
        </h2>
    </div>

    <div class="card-body">
        @if (Count($vereadores) == 0)
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
                        @foreach ($vereadores as $vereador)
                            <tr>
                                <td><strong>{{ $vereador->id_user != null ? $vereador->usuario->pessoa->nomeCompleto : 'não informado' }}</strong></td>
                                <td>
                                    Primeiro turno: <strong>{{ $vereador->dataInicioMandato != null ? date('d/m/Y', strtotime($vereador->dataInicioMandato)) : 'não informado' }} </strong><br>
                                    Segundo turno: <strong>{{ $vereador->dataFimMandato != null ? date('d/m/Y', strtotime($vereador->dataFimMandato)) : 'não informado' }} </strong>
                                </td>
                                <td><strong>{{ $vereador->cargo_eletivo->descricao }}</strong></td>
                                <td>
                                    <strong>{{ $vereador->cadastradoPorUsuario != null ? $vereador->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $vereador->created_at != null ? $vereador->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('vereador.edit', $vereador->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $vereador->id }}">Excluir</button>
                                </td>
                                {{-- $table->date('')->nullable();
                                $table->date('')->nullable();
                                $table->integer('id_cargo_eletivo')->unsigned()->nullable();
                                $table->integer('id_pleito_eleitoral')->unsigned()->nullable();
                                $table->uuid('id_user')->nullable(); --}}
                                {{-- <td><strong>{{ $vereador->ano_pleito != null ? $vereador->ano_pleito : 'não informado' }}</strong></td>
                                <td>Início: <strong>{{ $vereador->inicio_mandato }}</strong> - Fim: <strong>{{ $vereador->fim_mandato }}</strong></td>
                                <td>
                                    Primeiro turno: <strong>{{ $vereador->dataPrimeiroTurno != null ? date('d/m/Y', strtotime($vereador->dataPrimeiroTurno)) : 'não informado' }} </strong><br>
                                    Segundo turno: <strong>{{ $vereador->dataSegundoTurno != null ? date('d/m/Y', strtotime($vereador->dataSegundoTurno)) : 'não informado' }} </strong>
                                </td>
                                <td>
                                    @if (Count($vereador->cargos_eletivos_ativos()) != 0)
                                        <ol>
                                            @foreach ($vereador->cargos_eletivos_ativos() as $vereador_cargo)
                                                <li>{{ $vereador_cargo->cargo_eletivo->descricao }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        Nenhum cargo eletivo cadastrado
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $vereador->cadastradoPorUsuario != null ? $vereador->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $vereador->created_at != null ? $vereador->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('vereador.edit', $vereador->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $vereador->id }}">Excluir</button>
                                </td> --}}
                                {{-- Início: {{ date('d/m/Y', strtotime($vereador->inicio_mandato)) }} - Fim: {{ date('d/m/Y', strtotime($vereador->fim_mandato)) }} --}}
                                {{-- <td>{{ $vereador->id_tipo_pleito != null ? $vereador->tipo_pleito->descricao : 'não informado' }}</td>
                                <td>
                                    <strong>{{ $vereador->cadastradoPorUsuario != null ? $vereador->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $vereador->created_at != null ? $vereador->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('configuracao.pleito.edit', $vereador->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $vereador->id }}">Excluir</button>
                                </td> --}}
                            </tr>

                            <div class="modal fade" id="exampleModalExcluir{{ $vereador->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('vereador.destroy', $vereador->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    <strong style="font-size: 1.2rem">Excluir Pleito de <i>{{ $vereador->ano_pleito != null ? $vereador->ano_pleito : 'não informado' }}</i></strong>
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
        <a href="{{ route('vereador.create') }}" class="btn btn-primary">Cadastrar Vereador</a>
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
