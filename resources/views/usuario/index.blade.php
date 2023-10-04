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

    <div class="card" style="background-color:white">

        <div class="card-header" style="background-color:white">
            <h2 class="text-center">
                <div>
                    <span><i class="fas fa-address-book"></i></span>
                </div>
                <strong>Listagem de Usuários</strong>
            </h2>
        </div>

        <div class="card-body">
            @if (Count($usuarios) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no
                        sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">CPF</th>
                                <th scope="col">Email</th>
                                <th scope="col">Perfis ativos</th>
                                <th scope="col">Bloqueado (para desbloquear o usuário, clique no botão "Sim")</th>
                                <th scope="col">Cadastrado em</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}
                                    </td>
                                    <td class="cpf">{{ $usuario->cpf != null ? $usuario->cpf : 'não informado' }}</td>
                                    <td>{{ $usuario->email != null ? $usuario->email : 'não informado' }}</td>
                                    <td>
                                        <ol>
                                            @foreach ($usuario->permissoes_ativas as $permissao)
                                                <li>
                                                    {{ $permissao->perfil->descricao }}
                                                </li>
                                            @endforeach
                                        </ol>
                                    </td>
                                    <td>
                                        @if ($usuario->bloqueadoPorTentativa == true)
                                            <button type="button" class="btn btn-dark m-1" data-toggle="modal"
                                                data-target="#exampleModalDesbloquear{{ $usuario->id }}">Sim</button>
                                        @else
                                            <button type="button" class="btn btn-info">
                                                Não
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <strong>{{ $usuario->cadastradoPorUsuario != null ? $usuario->cad_usuario->pessoa->nomeCompleto : 'Sistema' }}</strong> --}}
                                        <strong>{{ $usuario->created_at != null ? $usuario->created_at->format('d/m/Y H:i:s') : 'sem registro' }}</strong>
                                    </td>
                                    <td>
                                        @if ($usuario->ativo == 1)
                                            <a href="{{ route('usuario.edit', $usuario->id) }}"
                                                class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                            @if (Auth::user()->temPermissao('User', 'Exclusão') == 1)
                                                <button type="button" class="btn btn-danger m-1" data-toggle="modal"
                                                    data-target="#exampleModalExcluir{{ $usuario->id }}"><i class="fas fa-trash"></i></button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-danger m-1">
                                                Excluído por
                                                <strong>{{ $usuario->inativadoPorUsuario != null ? $usuario->inativadoPor->pessoa->nomeCompleto : 'não informado' }}</strong>
                                                em
                                                <strong>{{ date('d/m/Y H:i:s', strtotime($usuario->dataInativado)) }}</strong>
                                                <br>
                                                Motivo: {{ $usuario->motivoInativado }}</strong>
                                            </button>
                                            @if (Auth::user()->temPermissao('User', 'Exclusão') == 1)
                                                <button type="button" class="btn btn-primary m-1" data-toggle="modal"
                                                    data-target="#exampleModalRecadastrar{{ $usuario->id }}">Recadastrar</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                                <div class="modal fade" id="exampleModalExcluir{{ $usuario->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits"
                                                action="{{ route('usuario.destroy', $usuario->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        <strong style="font-size: 1.2rem">Excluir
                                                            <i>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</i></strong>
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="motivo" class="form-label">Motivo</label>
                                                        <input type="text" class="form-control" name="motivo" required>
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

                                <div class="modal fade" id="exampleModalDesbloquear{{ $usuario->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalLabelDesbloquear" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits"
                                                action="{{ route('usuario.desbloquear', $usuario->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-primary">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelDesbloquear">
                                                        <strong style="font-size: 1.2rem">Desbloqueio</strong>
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    Deseja desbloquear o usuário: <strong>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancelar
                                                    </button>
                                                    <button type="submit"
                                                        class="button_submit btn btn-primary">Desbloquear</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="exampleModalRecadastrar{{ $usuario->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalLabelRecadastrar" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits"
                                                action="{{ route('usuario.restore', $usuario->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-primary">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelRecadastrar">
                                                        <strong style="font-size: 1.2rem">Recadastrar
                                                            <i>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</i></strong>
                                                    </h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancelar
                                                    </button>
                                                    <button type="submit"
                                                        class="button_submit btn btn-primary">Recadastrar</button>
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
            <a href=" {{ route('usuario.create') }} " class="btn btn-primary">Cadastrar novo Funcionário</a>
        </div>

    </div>

    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('jquery-mask/src/jquery.mask.js') }}"></script>

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
