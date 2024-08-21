@extends('layout.main')

<style>
    /* Define a classe para o estado selecionado */
    .selected {
        background-color: blue;
        color: white;
    }
    /* Opcional: Ajustar a cor do texto para branco quando selecionado */
    .selected td {
        color: white;
    }
    .selectable td {
        cursor: pointer;
    }

    .selectableTwo td {
        cursor: pointer;
    }
</style>

@section('content')
    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Departamentos</h1>
    <div class="card" style="background-color:white">
        <div id="accordion2">
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion2">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatables-reponsive2" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Departamento</th>
                                    <th scope="col">Cadastrado por</th>
                                    <th scope="col">Usuários vinculados</th>
                                    <th scope="col">Editar</th>
                                    <th scope="col">Excluir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departamentos as $departamento)
                                    <tr>
                                        <td>{{ $departamento->descricao != null ? $departamento->descricao : 'não informado' }}</td>
                                        <td>
                                            <strong>{{ $departamento->cadastradoPorUsuario != null ? $departamento->cad_usuario->pessoa->nome : 'cadastrado pelo sistema' }}</strong>
                                            em
                                            <strong>{{ $departamento->created_at != null ? $departamento->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModalVisualizar{{ $departamento->id }}">Visualizar</i></button>
                                        </td>
                                        <td>
                                            <a href="{{ route('configuracao.departamento.edit', $departamento->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalExcluir{{ $departamento->id }}"><i class="align-middle me-2 fas fa-fw fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="exampleModalExcluir{{ $departamento->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST" class="form_prevent_multiple_submits" action="{{ route('configuracao.departamento.destroy', $departamento->id) }}">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="modal-header btn-danger">
                                                        <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                            Excluir departamento <strong>{{ $departamento->descricao != null ? $departamento->descricao : 'não informado' }}</strong>?
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

                                    <div class="modal fade" id="exampleModalVisualizar{{ $departamento->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelVisualizar" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header btn-info">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        Usuários ativos no departamento <strong>{{$departamento->descricao}}</strong>
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    @if (count($departamento->usuarios) != null)
                                                        @foreach ($departamento->usuarios as $usuario)
                                                            <ul>
                                                                <li>
                                                                    {{ $usuario->pessoa->nome }}
                                                                </li>
                                                            </ul>
                                                        @endforeach
                                                    @else
                                                        Sem usuários vinculados
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">voltar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('configuracao.departamento.create') }}" class="btn btn-primary">Cadastrar departamento</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.cpf').mask('000.000.000-00');
        $('.cnpj').mask('00.000.000/0000-00');

        $(document).ready(function() {

            $('.selectable').on('click', 'td', function() {
                var tr = $(this).closest('tr');
                var checkbox = tr.find('input[type="checkbox"]');

                checkbox.prop('checked', !checkbox.prop('checked'));
                tr.toggleClass('selected', checkbox.prop('checked'));
            });

            $('input[type="checkbox"]').on('click', function(e) {
                e.stopPropagation(); // Previne que o clique no checkbox também acione o clique no <tr>

                var tr = $(this).closest('tr');
                tr.toggleClass('selected', this.checked);
            });

            $('.select2').select2({
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    }
                },
                closeOnSelect: true,
                width: '100%',
            });

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

            $('#datatables-reponsive2').dataTable({
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
