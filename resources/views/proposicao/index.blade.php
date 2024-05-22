@extends('layout.main')

@section('content')

@include('errors.alerts')
{{-- @include('errors.errors') --}}

<h1 class="h3 mb-3">Proposições</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        @if (Count($proposicaos) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
        <div class="table-responsive">
            <table id="datatables-reponsive" class="table" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Localização</th>
                        <th scope="col">Status</th>
                        <th scope="col">Cadastrado por</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposicaos as $proposicao)
                        <tr>
                            <td>{{ $proposicao->titulo }}</td>
                            <td>{{ $proposicao->id_localizacao != null ? $proposicao->localizacao->descricao : 'não informado' }}</td>
                            <td>{{ $proposicao->id_status != null ? $proposicao->status->descricao : 'não informado' }}</td>
                            <td>
                                <strong>{{ $proposicao->cadastradoPorUsuario != null ? $proposicao->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                em <strong>{{ $proposicao->created_at != null ? $proposicao->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('proposicao.edit', $proposicao->id) }}" class="btn btn-warning m-1"><i class="fas fa-pen"></i></a>
                                <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $proposicao->id }}"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>

                        <div class="modal fade" id="exampleModalExcluir{{ $proposicao->id }}"
                            tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="POST" class="form_prevent_multiple_submits" action="{{ route('proposicao.destroy', $proposicao->id) }}">
                                        @csrf
                                        @method('POST')
                                        <div class="modal-header btn-danger">
                                            <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                Excluir Proposição - <strong>{{ $proposicao->assunto != null ? $proposicao->assunto : 'não informado' }}</strong>
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
        @endif
    </div>

    <div class="card-footer">
        <a href="{{ route('proposicao.create') }}" class="btn btn-primary">Cadastrar Proposicão</a>
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
