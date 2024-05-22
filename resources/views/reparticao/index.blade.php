@extends('layout.main')

@section('content')

    @include('errors.alerts')
    {{-- @include('errors.errors') --}}

    <h1 class="h3 mb-3">Repartições</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            @if (Count($reparticaos) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Descrição</th>
                                <th scope="col">Tipo de Repartição</th>
                                <th scope="col">Cadastrado por</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reparticaos as $reparticao)
                                <tr>
                                    <td>{{ $reparticao->descricao }}</td>
                                    <td>{{ $reparticao->id_tipo_reparticao != null ? $reparticao->tipo_reparticao->descricao : 'não informado' }}</td>
                                    <td>
                                        <strong>{{ $reparticao->cadastradoPorUsuario != null ? $reparticao->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                        em <strong>{{ $reparticao->created_at != null ? $reparticao->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('reparticao.edit', $reparticao->id) }}" class="btn btn-warning m-1"><i class="fas fa-pen"></i></a>
                                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $reparticao->id }}"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="exampleModalExcluir{{ $reparticao->id }}"
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
            <a href="{{ route('reparticao.create') }}" class="btn btn-primary">Cadastrar Repartição</a>
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
