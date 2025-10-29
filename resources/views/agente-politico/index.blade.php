@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <h1 class="h3 mb-3">Agentes Políticos</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            @if (Count($agente_politicos) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead class="table-light">
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
                                    <td><strong>{{ $agente->id_user != null ? $agente->usuario->pessoa->nome : 'não informado' }}</strong></td>
                                    <td>
                                        Início: <strong>{{ $agente->dataInicioMandato != null ? date('d/m/Y', strtotime($agente->dataInicioMandato)) : 'não informado' }} </strong><br>
                                        Fim: <strong>{{ $agente->dataFimMandato != null ? date('d/m/Y', strtotime($agente->dataFimMandato)) : 'não informado' }} </strong>
                                    </td>
                                    <td><strong>{{ $agente->cargo_eletivo->descricao }}</strong></td>
                                    <td>
                                        <strong>{{ $agente->cadastradoPorUsuario != null ? $agente->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                        em <strong>{{ $agente->created_at != null ? $agente->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        {{-- <a href="{{ route('agente_politico.edit', $agente->id) }}" class="btn btn-warning m-1">Alterar</a> --}}
                                        <a href="{{ route('agente_politico.edit', $agente->id_user) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $agente->id_user }}"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="exampleModalExcluir{{ $agente->id_user }}"
                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('agente_politico.destroy', $agente->id_user) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        Excluir: <strong>{{ $agente->cargo_eletivo->descricao != null ? $agente->cargo_eletivo->descricao : 'não informado' }}</strong> <strong>{{ $agente->id_user != null ? $agente->usuario->pessoa->nome : 'não informado' }}</strong>
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

@endsection

@section('scripts')
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
