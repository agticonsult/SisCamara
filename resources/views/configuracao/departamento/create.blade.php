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

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Cadastro de Departamento</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <form action="{{ route('configuracao.departamento.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Nome</label>
                            <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao" placeholder="Informe o nome do departamento" value="{{ old('descricao') }}">
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="id_coordenador">Coordenador</label>
                            <select name="id_coordenador" class="form-control @error('id_coordenador') is-invalid @enderror select2">
                                <option value="" selected disabled>-- Selecione --</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('id_coordenador') == $usuario->id ? 'selected' : '' }}>{{ $usuario->pessoa->nome }}</option>
                                @endforeach
                            </select>
                            @error('id_coordenador')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <hr>
                            <h4>Usuário(s)</h4>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="table-responsive">
                                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr class="selectable">
                                            <th scope="col">Nome</th>
                                            <th scope="col">CPF/CNPJ</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Vincular</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($usuarios as $usuario)
                                            <tr class="selectable">
                                                <td >
                                                    {{ $usuario->pessoa->nome }}
                                                </td>
                                                <td class="masc">
                                                    @if ($usuario->pessoa->pessoaJuridica == 1)
                                                        <span class="cnpj">{{ $usuario->cnpj != null ? $usuario->cnpj : 'não informado' }}</span>
                                                    @else
                                                        <span class="cpf">{{ $usuario->cpf != null ? $usuario->cpf : 'não informado' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $usuario->email }}
                                                </td>
                                                <td style="text-align: center">
                                                    <input type="checkbox" name="usuario_selecionados[]" value="{{ $usuario->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                    <a href="{{ route('configuracao.departamento.index') }}" class="btn btn-light">Voltar</a>
                </div>
            </form>
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
        });
    </script>
@endsection
