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

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Alteração Departamento</h1>
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
                            <div class="form-group col-md-12">
                                <hr>
                                <h4>Usuário(s) vinculado(s)</h4>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="table-responsive">
                                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                        <thead class="table-light">
                                            <tr class="selectable">
                                                <th scope="col">Nome</th>
                                                <th scope="col">CPF/CNPJ</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Desvincular</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($departamento->usuarios as $usuarioDepartamento)
                                                <tr class="selectable">
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
                                                    <td style="text-align: center">
                                                        <input type="checkbox" name="usuario_selecionados[]" value="{{ $usuarioDepartamento->id }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
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
            <div class="form-group col-md-12">
                <form action="{{ route('configuracao.departamento.vincularUsuario', $departamento->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <h4>Usuário(s) não vinculado(s) ao departamento</h4>
                    <br>
                    <div class="table-responsive">
                        <table id="datatables-reponsive2" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr class="selectableTwo">
                                    <th scope="col">Nome</th>
                                    <th scope="col">CPF</th>
                                    <th scope="col">E-mail</th>
                                    <th scope="col">Vincular</th>
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
                                    <tr class="selectableTwo">
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
                                            <input type="checkbox" id="checkboxVincular" name="vincular_usuarios[]" value="{{ $usuario->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <button type="submit" class="button_submit btn btn-primary" id="saveButton" style="display: none;">Salvar</button>
                        </div>
                    </div>
                </form>
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

            $('.selectableTwo').on('click', 'td', function() {
                var tr = $(this).closest('tr');
                var checkbox2 = tr.find('input[type="checkbox"]');

                checkbox2.prop('checked', !checkbox2.prop('checked'));

                tr.toggleClass('selected', checkbox2.prop('checked'));

                var anyCheckBoxChecked = $("input[name='vincular_usuarios[]']:checked");

                if (anyCheckBoxChecked.length > 0) {
                    $('#saveButton').show();
                }else {
                    $('#saveButton').hide();
                }
            });

            $('input[type="checkbox"]').on('click', function(e) {
                e.stopPropagation();

                var tr = $(this).closest('tr');
                tr.toggleClass('selected', this.checked);

                var anyCheckBoxChecked = $("input[name='vincular_usuarios[]']:checked");

                if (anyCheckBoxChecked.length > 0) {
                    $('#saveButton').show();
                }else {
                    $('#saveButton').hide();
                }
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
