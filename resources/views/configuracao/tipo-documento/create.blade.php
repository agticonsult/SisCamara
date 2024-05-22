@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Cadastro Tipo de Documento</h1>
    @if (!isset($departamentos[0]))
        <div class="alert alert-warning alert-dismissible" role="alert">
            <div class="alert-message">
                <strong>Sem DEPARTAMENTO cadastrado!</strong>
                <a href="{{ route('configuracao.departamento.index') }}">Clique aqui para cadastrar</a>
            </div>
        </div>
    @endif

    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('configuracao.tipo_documento.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">*Nome</label>
                            <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe o nome" value="{{ old('nome') }}">
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">*Tipo de Documento</label>
                            <input class="form-control @error('tipoDocumento') is-invalid @enderror" type="text" name="tipoDocumento" id="tipoDocumento" placeholder="Informe o nome do Tipo de Documento" value="{{ old('tipoDocumento') }}">
                            @error('tipoDocumento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label mb-0">*Nível (número inteiro)</label>
                            <h5 class="mt-0 mb-1" style="font-size: 12px; color: rgb(146, 146, 146)">O nível do tipo do documento define a quantidade de departamentos que existirão na sua tramitação</h5>
                            <input class="form-control integer-mask @error('nivel') is-invalid @enderror" type="text" name="nivel" id="nivel" placeholder="Informe o nível do Tipo de Documento" value="{{ old('nivel') }}">
                            @error('nivel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="row" id="departamentosDiv"></div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            <a href="{{ route('configuracao.tipo_documento.index') }}" class="btn btn-light m-1">Voltar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
    <script>
        $('.integer-mask').mask('00');

        function limparDepartamentos() {
            var deps = $('#departamentosDiv').children();

            for (let i = 0; i < deps.length; i++) {
                $(deps[i]).fadeOut(300, function() {
                    $(deps[i]).remove();
                });
            }
        }

        function adicionarDepartamento(contador) {
            var dep = `<div class="col-md-6 mb-2 departamento">
                <label class="form-label">*Tramitação: Departamento ` + contador + `</label>
                <select name="id_departamento[]" class="form-control select2 @error('id_departamento') is-invalid @enderror">
                    <option value="" selected>-- selecione --</option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}">
                            {{ $departamento->descricao }}
                        </option>
                    @endforeach
                </select>
                @error('id_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>`;

            $('#departamentosDiv')
            .append($(dep)
                .hide()
                .fadeIn(300)
            );
        }

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

            let nivel = $('#nivel').val();

            if (nivel > 0) {
                for (let j = 1; j <= nivel; j++) {
                    adicionarDepartamento(j);
                }
            }

            $('#nivel').on('input', function () {
                limparDepartamentos();
                for (let i = 1; i <= this.value; i++) {
                    adicionarDepartamento(i);
                }
            });
        });

    </script>
@endsection
