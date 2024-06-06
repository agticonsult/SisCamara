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

    <h1 class="h3 mb-3">Alteração Departamento</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('configuracao.departamento.update', $departamento->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Nome</label>
                                <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao" value="{{ $departamento->descricao != null ? $departamento->descricao : old('descricao') }}" placeholder="Nome do departamento">
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="id_coordenador">Coordenador</label>
                                <select name="id_coordenador" class="form-control @error('id_coordenador') is-invalid @enderror select2">
                                    <option value="" selected disabled>-- Selecione --</option>
                                    @php
                                        $coordenadores = array();
                                        foreach ($users as $user) {
                                            if ($user->usuarioInterno() == 1) {
                                                array_push($coordenadores, $user);
                                            }
                                        }
                                    @endphp
                                    @foreach ($coordenadores as $coordenador)
                                        @php
                                            $temCoordenador = 0;
                                            if ($departamento->id_coordenador == $coordenador->id) {
                                                $temCoordenador = 1;
                                            }
                                        @endphp
                                        @if ($temCoordenador == 1)
                                            <option value="{{ $coordenador->id }}" selected>{{ $coordenador->pessoa->nome }}</option>
                                        @else
                                            <option value="{{ $coordenador->id }}">{{ $coordenador->pessoa->nome }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('id_coordenador')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class=".form-group col-md-12">
                                <hr>
                                <h4>Usuário(s) não vinculado(s)</h4>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="table-responsive">
                                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                        <thead class="table-light">
                                            <tr class="selectable">
                                                <th scope="col">Nome</th>
                                                <th scope="col">CPF/CNPJ</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Selecionar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $usuarios = array();
                                                foreach ($users as $user) {
                                                    if ($user->usuarioInterno() == 1 && $user->estaVinculadoDep($departamento->id)) {
                                                        array_push($usuarios, $user);
                                                    }
                                                }
                                            @endphp
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

                                                    <td style="text-align: center">
                                                        <input type="checkbox" name="usuario_selecionados[]" value="{{ $usuario->id }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                                <a href="{{ route('configuracao.departamento.index') }}" class="btn btn-light">Voltar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <h4>Usuário(s) vinculado(s) ao departamento</h4>
                <br>
                <div class="table-responsive">
                    <table id="datatables-reponsive2" class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">CPF</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Desvincular</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departamento->usuarios as $usuarioDepartamento)
                                <tr>
                                    <td >
                                        {{ $usuarioDepartamento->pessoa->nome }}
                                    </td>
                                    <td class="masc">
                                        @if ($usuarioDepartamento->pessoa->pessoaJuridica == 1)
                                            <span class="cnpj">{{ $usuarioDepartamento->cnpj != null ? $usuarioDepartamento->cnpj : 'não informado' }}</span>
                                        @else
                                            <span class="cpf">{{ $usuarioDepartamento->cpf != null ? $usuarioDepartamento->cpf : 'não informado' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $usuarioDepartamento->email }}
                                    </td>
                                    <td>
                                        <button type="button" class="desativar btn btn-danger" name="{{ $usuarioDepartamento->id_user }}" data-toggle="modal" data-target="#exampleModalExcluir{{ $usuarioDepartamento->id }}">
                                            <i class="align-middle me-2 fas fa-fw fa-trash"></i>
                                        </button>
                                        {{-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalExcluir{{ $usuarioDepartamento->id }}"><i class="align-middle me-2 fas fa-fw fa-trash"></i></button> --}}
                                    </td>
                                </tr>
                                <div class="modal fade" id="exampleModalExcluir{{ $usuarioDepartamento->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('configuracao.departamento.desvincularUsuario', $usuarioDepartamento->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        Desvincular do departamento: <strong>{{ $usuarioDepartamento->pessoa->nome }} - {{ $usuarioDepartamento->email }}</strong>?
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
            </div>
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
