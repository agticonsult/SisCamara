@extends('layout.main')

@section('content')

    @include('errors.alerts')
    {{-- @include('errors.errors') --}}
    <h1 class="h3 mb-3">Documentos</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            @if (Count($documentos) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Tipo de documento</th>
                                <th>Status</th>
                                <th>Cadastrado por</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documentos as $documento)
                                <tr>
                                    <td>{{ $documento->titulo }}</td>
                                    <td>
                                        {{ $documento->id_tipo_documento != null ? $documento->tipoDocumento->nome : 'não informado' }} <br>
                                        <strong>{{ $documento->id_tipo_workflow != null ? $documento->tipoWorkflow->descricao : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        @if ($documento->reprovado_em_tramitacao)
                                            O documento foi reprovado em tramitação
                                        @else
                                            @if ($documento->finalizado)
                                                O documento foi finalizado
                                            @else
                                                O documento está em tramitação
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $documento->cadastradoPorUsuario != null ? $documento->cad_usuario->pessoa->nome : 'não informado' }}</strong> em <br>
                                        <strong>{{ $documento->created_at != null ? $documento->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        @if ($documento->reprovado_em_tramitacao)
                                            @if ($documento->cadastradoPorUsuario == auth()->user()->id)
                                                <a href="{{ route('documento.edit', $documento->id) }}" class="btn btn-warning m-1">Editar</a>
                                            @endif
                                        @endif
                                        <a href="{{ route('documento.show', $documento->id) }}" class="btn btn-info m-1"><i class="fas fa-eye"></i></a>
                                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $documento->id }}"><i class="fas fa-trash"></i></button>
                                        {{-- <a href="{{ route('documento.acompanharDoc', $documento->id) }}" class="btn btn-info m-1">Acompanhar documento</a> --}}
                                    </td>
                                </tr>

                                {{-- <div class="modal fade" id="exampleModalExcluir{{ $reparticao->id }}"
                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('reparticao.destroy', $reparticao->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        Excluir <strong>{{ $reparticao->descricao != null ? $reparticao->descricao : 'não informado' }} - {{ $reparticao->id_tipo_reparticao != null ? $reparticao->tipo_reparticao->descricao : 'não informado' }}</strong>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card-footer">
            <a href="{{ route('documento.create') }}" class="btn btn-primary">Cadastrar documento</a>
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
