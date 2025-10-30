@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Tipo de Documento</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            @if (Count($tipoDocumentosAtivos) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">Tipo de documento</th>
                                <th scope="col">Nível</th>
                                <th scope="col">Tramitação</th>
                                <th scope="col">Cadastrado por</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tipoDocumentosAtivos as $tp)
                                <tr>
                                    <td>{{ $tp->nome != null ? $tp->nome : '-' }}</td>
                                    <td>{{ $tp->tipoDocumento != null ? $tp->tipoDocumento : '-' }}</td>
                                    <td>{{ $tp->nivel != null ? $tp->nivel : '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModalVisualizar{{ $tp->id }}">Visualizar</i></button>
                                    </td>
                                    <td>
                                        <strong>{{ $tp->cadastradoPorUsuario != null ? $tp->cad_usuario->pessoa->nome : '-' }}</strong> em
                                        <strong>{{ $tp->created_at != null ? $tp->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('configuracao.tipo_documento.edit', $tp->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $tp->id }}"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <div class="modal fade" id="exampleModalVisualizar{{ $tp->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelVisualizar" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header btn-info">
                                                <h5 class="modal-title text-center" id="exampleModalLabelVisualizar">
                                                    <strong>Tramitações</strong>
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                            @if (count($tp->departamentoVinculados) != null)
                                                    <ol>
                                                        @foreach ($tp->departamentoVinculados as $dpVinc)
                                                            <li>
                                                                {{ $dpVinc->departamento->descricao }}
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                            @else
                                                    Sem tramitações
                                            @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">voltar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="exampleModalExcluir{{ $tp->id }}"
                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('configuracao.tipo_documento.destroy', $tp->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        Excluir: <strong>{{ $tp->nome != null ? $tp->nome : 'não informado' }}</strong>
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
            <a href="{{ route('configuracao.tipo_documento.create') }}" class="btn btn-primary">Cadastrar</a>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
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
