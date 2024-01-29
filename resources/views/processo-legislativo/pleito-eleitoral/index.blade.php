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

<h1 class="h3 mb-3">Pleitos Eleitorais</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        @if (Count($pleitos) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Ano Pleito</th>
                            <th scope="col">Legislatura</th>
                            <th scope="col">Turnos</th>
                            <th scope="col">Cargos Eletivos</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pleitos as $pleito)
                            <tr>
                                <td><strong>{{ $pleito->ano_pleito != null ? $pleito->ano_pleito : 'não informado' }}</strong></td>
                                <td>Início: <strong>{{ $pleito->legislatura->inicio_mandato }}</strong> - Fim: <strong>{{ $pleito->legislatura->fim_mandato }}</strong></td>
                                <td>
                                    Primeiro turno: <strong>{{ $pleito->dataPrimeiroTurno != null ? date('d/m/Y', strtotime($pleito->dataPrimeiroTurno)) : 'não informado' }} </strong><br>
                                    Segundo turno: <strong>{{ $pleito->dataSegundoTurno != null ? date('d/m/Y', strtotime($pleito->dataSegundoTurno)) : 'não informado' }} </strong>
                                </td>
                                <td>
                                    @if (Count($pleito->cargos_eletivos_ativos()) != 0)
                                        <ol>
                                            @foreach ($pleito->cargos_eletivos_ativos() as $pleito_cargo)
                                                <li>{{ $pleito_cargo->cargo_eletivo->descricao }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        Nenhum cargo eletivo cadastrado
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $pleito->cadastradoPorUsuario != null ? $pleito->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                    em <strong>{{ $pleito->created_at != null ? $pleito->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('processo_legislativo.pleito_eleitoral.edit', $pleito->id) }}" class="btn btn-warning m-1"><i class="fas fa-pen"></i></a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $pleito->id }}"><i class="fas fa-trash"></i></button>
                                </td>
                                {{-- Início: {{ date('d/m/Y', strtotime($pleito->inicio_mandato)) }} - Fim: {{ date('d/m/Y', strtotime($pleito->fim_mandato)) }} --}}
                                {{-- <td>{{ $pleito->id_tipo_pleito != null ? $pleito->tipo_pleito->descricao : 'não informado' }}</td>
                                <td>
                                    <strong>{{ $pleito->cadastradoPorUsuario != null ? $pleito->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                    em <strong>{{ $pleito->created_at != null ? $pleito->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('processo_legislativo.pleito.edit', $pleito->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $pleito->id }}">Excluir</button>
                                </td> --}}
                            </tr>

                            <div class="modal fade" id="exampleModalExcluir{{ $pleito->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('processo_legislativo.pleito_eleitoral.destroy', $pleito->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    Excluir Pleito Ano: <strong>{{ $pleito->ano_pleito != null ? $pleito->ano_pleito : 'não informado' }}</strong>
                                                    Início: <strong>{{ $pleito->legislatura->inicio_mandato }}</strong> - Fim: <strong>{{ $pleito->legislatura->fim_mandato }}</strong>
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
                                                    <strong style="font-size: 1.2rem">Recadastrar <i>{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : 'não informado' }}</i></strong>
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
        <a href="{{ route('processo_legislativo.pleito_eleitoral.create') }}" class="btn btn-primary">Cadastrar Pleito Eleitoral</a>
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
