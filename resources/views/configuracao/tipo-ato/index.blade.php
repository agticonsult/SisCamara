@extends('layout.main')

@section('content')
    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Listagem Tipos de Atos</h1>
    <div class="card" style="background-color:white">
        <div id="accordion3">
            <div class="card-header" id="headingThree">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree"
                        aria-expanded="false" aria-controls="collapseThree">
                        Cadastro
                    </button>
                </h5>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion3">
                <div class="card-body">
                    <form action="{{ route('configuracao.tipo_ato.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                        @csrf
                        @method('POST')

                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Nome</label>
                                    <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao"  placeholder="Informe o nome do tipo de ato" value="{{ old('descricao') }}">
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="background-color:white">
        <div id="accordion2">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo"
                        aria-expanded="false" aria-controls="collapseTwo">
                        Listagem
                    </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion2">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Tipo de Ato</th>
                                    <th scope="col">Cadastrado por</th>
                                    <th scope="col">Editar</th>
                                    <th scope="col">Excluir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tipoAtos as $tipo)
                                    <tr>
                                        <td>{{ $tipo->descricao != null ? $tipo->descricao : 'não informado' }}</td>
                                        <td>
                                            <strong>{{ $tipo->cadastradoPorUsuario != null ? $tipo->cad_usuario->pessoa->nome : 'cadastrado pelo sistema' }}</strong>
                                            em
                                            <strong>{{ $tipo->created_at != null ? $tipo->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                        </td>
                                        {{-- <td>
                                        <a href="{{ route('configuracao.finalidade_grupo.edit', $finalidade->id) }}"
                                        class="btn btn-warning">Alterar</a>
                                    </td> --}}
                                        <td>
                                            <a href="{{ route('configuracao.tipo_ato.edit', $tipo->id) }}"
                                                class="btn btn-warning"><i
                                                    class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                                data-target="#exampleModalExcluir{{ $tipo->id }}"><i
                                                class="align-middle me-2 fas fa-fw fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="exampleModalExcluir{{ $tipo->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" class="form_prevent_multiple_submits"
                                                    action="{{ route('configuracao.tipo_ato.destroy', $tipo->id) }}">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="modal-header btn-danger">
                                                        <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                            Excluir tipo de Ato: <strong>{{ $tipo->descricao != null ? $tipo->descricao : 'não informado' }}</strong>?
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="motivo" class="form-label">Motivo</label>
                                                            <input type="text" class="form-control" name="motivo">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancelar
                                                        </button>
                                                        <button type="submit"
                                                            class="button_submit btn btn-danger">Excluir</button>
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
