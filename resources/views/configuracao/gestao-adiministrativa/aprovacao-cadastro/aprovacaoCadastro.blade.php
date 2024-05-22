@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Aprovação de cadastros Usuários Externos</h1>

    <div class="card">
        <div class="card-body">

            @if (Count($usuarios) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros para realizar a aprovação.</h1>
                </div>
            @else
                <form action="{{ route('aprovacao_cadastro_usuario.storeCadastroUsuario') }}" method="post">
                    @csrf
                    @method('POST')

                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">CPF/CNPJ</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Cadastro Aprovado</th>
                                    <th scope="col">E-mail confirmado</th>
                                    <th scope="col">Cadastrado em</th>
                                    <th scope="col">Selecionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : 'não informado' }}</td>
                                        <td class="masc">
                                            @if ($usuario->pessoa->pessoaJuridica == 1)
                                                <span class="cnpj">{{ $usuario->cnpj != null ? $usuario->cnpj : 'não informado' }}</span>
                                            @else
                                                <span class="cpf">{{ $usuario->cpf != null ? $usuario->cpf : 'não informado' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $usuario->email != null ? $usuario->email : 'não informado' }}</td>
                                        <td>{{ $usuario->cadastroAprovado == 1 ? 'Sim' : 'Não' }}</td>
                                        <td>{{ $usuario->confirmacao_email == 1 ? 'Sim': 'Não' }}</td>
                                        <td>
                                            <strong>{{ $usuario->created_at != null ? $usuario->created_at->format('d/m/Y H:i:s') : 'sem registro' }}</strong>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="usuario_selecionados[]" value="{{ $usuario->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('.cpf').mask('000.000.000-00');
        $('.cnpj').mask('00.000.000/0000-00');

        $(document).ready(function() {

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
        });

    </script>
@endsection
