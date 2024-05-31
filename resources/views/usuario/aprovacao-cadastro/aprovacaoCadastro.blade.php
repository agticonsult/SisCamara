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
</style>
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
                <form action="{{ route('usuario.aprovacaoCadastroUsuario') }}" method="post">
                    @csrf
                    @method('POST')

                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr class="selectable">
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
                                    <tr class="selectable">
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
                                        <td style="text-align: center">
                                            <input type="checkbox" name="usuario_selecionados[]" value="{{ $usuario->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><br>
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

            // Evento de clique no <td> dentro de uma linha
            $('.selectable').on('click', 'td', function() {
                var tr = $(this).closest('tr');
                var checkbox = tr.find('input[type="checkbox"]');

                // Alternar estado do checkbox
                checkbox.prop('checked', !checkbox.prop('checked'));

                // Alternar a classe 'selected' na linha <tr>
                tr.toggleClass('selected', checkbox.prop('checked'));
            });

            // Para garantir que o clique direto no checkbox também funcione
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
        });

    </script>
@endsection
