@extends('layout.main')

@section('content')

    @include('errors.alerts')
    @include('errors.errors')

    <h1 class="h3 mb-3">Usuários</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            @if (Count($usuarios) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no
                        sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">CPF/CNPJ</th>
                                <th scope="col">Email</th>
                                <th scope="col">Perfis ativos</th>
                                {{-- <th scope="col">Bloqueado (para desbloquear o usuário, clique no botão)</th> --}}
                                <th scope="col">Cadastrado em</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : 'não informado' }}</td>
                                    {{-- <td class="masc">
                                        @if ($usuario->pessoa->pessoaJuridica == 1)
                                            <span class="cnpj">{{ $usuario->cnpj != null ? $usuario->cnpj : 'não informado' }}</span>
                                        @else
                                            <span class="cpf">{{ $usuario->cpf != null ? $usuario->cpf : 'não informado' }}</span>
                                        @endif
                                    </td> --}}
                                    <td class="masc">
                                        <span class="cpf">{{ $usuario->cpf != null ? $usuario->cpf : 'não informado' }}</span>
                                    </td>
                                    <td>{{ $usuario->email != null ? $usuario->email : 'não informado' }}</td>
                                    <td>
                                        <ul>
                                            @foreach ($usuario->permissoes_ativas as $permissao)
                                                <li>
                                                    {{ $permissao->perfil->descricao }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    {{-- <td>
                                        @if ($usuario->bloqueadoPorTentativa == true)
                                            <button type="button" class="btn btn-dark m-1" data-toggle="modal" data-target="#exampleModalDesbloquear{{ $usuario->id }}">Sim</button>
                                        @else
                                            <button type="button" class="btn btn-info">
                                                Não
                                            </button>
                                        @endif
                                    </td> --}}
                                    <td>
                                        {{-- <strong>{{ $usuario->cadastradoPorUsuario != null ? $usuario->cad_usuario->pessoa->nome : 'Sistema' }}</strong> --}}
                                        <strong>{{ $usuario->created_at != null ? $usuario->created_at->format('d/m/Y H:i:s') : 'sem registro' }}</strong>
                                    </td>
                                    <td>
                                        @if ($usuario->ativo == 1)
                                            @if ($usuario->bloqueadoPorTentativa == true)
                                                <button type="button" class="btn btn-dark m-1"
                                                    data-toggle="modal" data-target="#exampleModalDesbloquear{{ $usuario->id }}">
                                                    Desbloquear
                                                </button>
                                            @endif
                                            <a href="{{ route('usuario.edit', $usuario->id) }}" class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                            @if (Auth::user()->temPermissao('User', 'Exclusão') == 1)
                                                <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $usuario->id }}"><i class="fas fa-trash"></i></button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-danger m-1">
                                                Excluído por
                                                <strong>{{ $usuario->inativadoPorUsuario != null ? $usuario->inativadoPor->pessoa->nome : 'não informado' }}</strong>
                                                em
                                                <strong>{{ date('d/m/Y H:i:s', strtotime($usuario->dataInativado)) }}</strong>
                                                <br>
                                                Motivo: {{ $usuario->motivoInativado }}</strong>
                                            </button>
                                            @if (Auth::user()->temPermissao('User', 'Exclusão') == 1)
                                                <button type="button" class="btn btn-primary m-1" data-toggle="modal" data-target="#exampleModalRecadastrar{{ $usuario->id }}">Reativar usuário</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                                <div class="modal fade" id="exampleModalExcluir{{ $usuario->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('usuario.destroy', $usuario->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        Excluir: <strong>{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : 'não informado' }}</strong> - <strong>{{ $usuario->email != null ? $usuario->email : 'não informado' }}</strong>?
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

                                <div class="modal fade" id="exampleModalDesbloquear{{ $usuario->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelDesbloquear" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits"
                                                action="{{ route('usuario.desbloquear', $usuario->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-primary">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelDesbloquear">
                                                        <strong>Desbloqueio</strong>
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    Deseja desbloquear o usuário: <strong>{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : 'não informado' }}</strong>?
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

                                <div class="modal fade" id="exampleModalRecadastrar{{ $usuario->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelRecadastrar" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('usuario.restore', $usuario->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-warning">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelRecadastrar">
                                                        Reativar usuário? <strong>{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : 'não informado' }} - {{ $usuario->email != null ? $usuario->email : 'não informado' }}</strong>
                                                    </h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Não
                                                    </button>
                                                    <button type="submit" class="button_submit btn btn-success">Sim</button>
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
            <a href=" {{ route('usuario.create') }} " class="btn btn-primary">Cadastrar novo usuário</a>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('.cpf').mask('000.000.000-00');
        // $('.cnpj').mask('00.000.000/0000-00');

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
