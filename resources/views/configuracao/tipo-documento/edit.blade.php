@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Alteração Tipo de Documento</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('configuracao.tipo_documento.update', $tipoDocumento->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Nome</label>
                                    <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe o nome" value="{{ $tipoDocumento->nome }}">
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Tipo de Documento</label>
                                    <input class="form-control @error('tipoDocumento') is-invalid @enderror" type="text" name="tipoDocumento" id="tipoDocumento" placeholder="Informe o nome do Tipo de Documento" value="{{ $tipoDocumento->tipoDocumento }}">
                                    @error('tipoDocumento')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Departamentos Tramitações</label>
                                    <select name="id_departamento[]" class="form-control select2 @error('id_departamento') is-invalid @enderror" multiple>
                                        @foreach ($departamentos as $departamento)
                                            <option value="{{ $departamento->id }}">
                                                {{ $departamento->descricao }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_departamento')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
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
        });
    </script>
@endsection
