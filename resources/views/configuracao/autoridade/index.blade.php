@extends('layout.main')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css"
        integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ=="
        crossorigin="anonymous" />
    <style>
        .error {
            color: red
        }
    </style>
    @include('errors.alerts')
    @include('errors.errors')


    <h1 class="h3 mb-3">Autoridades</h1>
    <div class="card" style="background-color:white">
        <div id="accordion3">
            <div class="card">
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
                        <form action="{{ route('configuracao.autoridade.store') }}" id="form" method="POST"
                            class="form_prevent_multiple_submits">
                            @csrf
                            @method('POST')

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">*Nome</label>
                                        <input class="form-control" type="text" name="descricao" id="descricao"
                                            placeholder="Informe o nome da autoridade" value="{{ old('descricao') }}">
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

        <div id="accordion2">
            <div class="card">
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
                                <thead>
                                    <tr>
                                        <th scope="col">Nome da Autoridade</th>
                                        <th scope="col">Cadastrado por</th>
                                        <th scope="col">Editar</th>
                                        <th scope="col">Excluir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($autoridades as $autoridade)
                                        <tr>
                                            <td>{{ $autoridade->descricao != null ? $autoridade->descricao : 'não informado' }}</td>
                                            <td>
                                                <strong>{{ $autoridade->cadastradoPorUsuario != null ? $autoridade->cad_usuario->pessoa->nome : 'cadastrado pelo sistema' }}</strong>
                                                em
                                                <strong>{{ $autoridade->created_at != null ? $autoridade->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                            </td>
                                            {{-- <td>
                                            <a href="{{ route('configuracao.finalidade_grupo.edit', $finalidade->id) }}"
                                            class="btn btn-warning">Alterar</a>
                                        </td> --}}
                                            <td>
                                                <a href="{{ route('configuracao.autoridade.edit', $autoridade->id) }}"
                                                    class="btn btn-warning"><i
                                                        class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                                    data-target="#exampleModalExcluir{{ $autoridade->id }}"><i
                                                    class="align-middle me-2 fas fa-fw fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="exampleModalExcluir{{ $autoridade->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="POST" class="form_prevent_multiple_submits"
                                                        action="{{ route('configuracao.autoridade.destroy', $autoridade->id) }}">
                                                        @csrf
                                                        @method('POST')
                                                        <div class="modal-header btn-danger">
                                                            <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                                Excluir <strong>{{ $autoridade->descricao != null ? $autoridade->descricao : 'não informado' }}</strong>?
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

    </div>

    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('jquery-mask/src/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <script>
        $("#form").validate({
            rules: {
                descricao: {
                    required: true,
                    maxlength: 255,
                }
            },
            messages: {
                descricao: {
                    required: "Campo obrigatório",
                    maxlength: "Máximo de 255 caracteres"
                }
            }
        });

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
