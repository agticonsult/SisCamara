@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Ato > </span>Cadastrar de Ato</h1>

    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('ato.store') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <h3>Texto</h3>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label">*Título</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" name="titulo" value="{{ old('titulo') }}">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" class="form-control @error('subtitulo') is-invalid @enderror" name="subtitulo" value="{{ old('subtitulo') }}">
                            @error('subtitulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label">*Corpo do Texto</label>
                            <textarea name="corpo_texto" cols="10" rows="10" class="form-control @error('corpo_texto') is-invalid @enderror">{{ old('corpo_texto') }}</textarea>
                            @error('corpo_texto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <br><hr>

                    <h3>Dados Gerais</h3>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">*Classificação do Ato</label>
                            <select name="id_classificacao" class="select2 form-control @error('id_classificacao') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($classificacaos as $classificacao)
                                    <option value="{{ $classificacao->id }}" {{ old('id_classificacao') == $classificacao->id ? 'selected' : '' }}>{{ $classificacao->descricao }}</option>
                                @endforeach
                            </select>
                            @error('id_classificacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Ano</label>
                            <input type="text" class="form-control @error('ano') is-invalid @enderror" name="ano" id="ano" value="{{ old('ano') }}">
                            @error('ano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Número</label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror" name="numero" value="{{ old('numero') }}">
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        {{-- <div class="form-group col-md-4">
                            <label class="form-label">*Grupo</label>
                            <select name="id_grupo" class="select2 form-control @error('id_grupo') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" {{ old('id_grupo') == $grupo->id ? 'selected' : '' }}>{{ $grupo->nome }}</option>
                                @endforeach
                            </select>
                            @error('id_grupo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}
                        <div class="form-group col-md-4">
                            <label class="form-label">Data de Publicação</label>
                            <input type="date" class="form-control @error('data_publicacao') is-invalid @enderror" name="data_publicacao" value="{{ old('data_publicacao') }}">
                            @error('data_publicacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Tipo de Ato</label>
                            <select name="id_tipo_ato" class="select2 form-control @error('id_tipo_ato') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($tipo_atos as $tipo_ato)
                                    <option value="{{ $tipo_ato->id }}" {{ old('id_tipo_ato') == $tipo_ato->id ? 'selected' : '' }}>{{ $tipo_ato->descricao }}</option>
                                @endforeach
                            </select>
                            @error('id_tipo_ato')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Assunto</label>
                            <select name="id_assunto" class="select2 form-control @error('id_assunto') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($assuntos as $assunto)
                                    <option value="{{ $assunto->id }}" {{ old('id_assunto') == $assunto->id ? 'selected' : '' }}>{{ $assunto->descricao }}</option>
                                @endforeach
                            </select>
                            @error('id_assunto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">*Órgão que editou o ato</label>
                            <select name="id_orgao" class="select2 form-control @error('id_orgao') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($orgaos as $orgao)
                                    <option value="{{ $orgao->id }}" {{ old('id_orgao') == $orgao->id ? 'selected' : '' }}>{{ $orgao->descricao }}</option>
                                @endforeach
                            </select>
                            @error('id_orgao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Forma de Publicação</label>
                            <select name="id_forma_publicacao" class="select2 form-control @error('id_forma_publicacao') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($forma_publicacaos as $forma_publicacao)
                                    <option value="{{ $forma_publicacao->id }}" {{ old('id_forma_publicacao') == $forma_publicacao->id ? 'selected' : '' }}>{{ $forma_publicacao->descricao }}</option>
                                @endforeach
                            </select>
                            @error('id_forma_publicacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-check col-md-6">
                        <input type="checkbox" class="form-check-input" id="altera_dispositivo" name="altera_dispositivo" value="{{ old('altera_dispositivo') }}">
                        <label class="form-check-label" for="altera_dispositivo">Este ato altera algum dispositivo legal</label>
                    </div>

                    <br><hr>

                    <h3>Anexo</h3>
                    <div class="col-12">
                        <br> Observações
                        <ul>
                            <li>Tamanho máximo do anexo: {{ $filesize->mb }}MB</li>
                        </ul>
                        Extensões permitidas
                        <ul>
                            <li>Documento (txt,pdf,xls,xlsx,doc,docx,odt)</li>
                            <li>Imagem (jpg,jpeg,png)</li>
                            {{-- <li>Áudio (mp3)</li>
                            <li>Vídeo (mp4, mkv)</li> --}}
                        </ul>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="anexo">Arquivo</label>
                            <input type="file" name="anexo[]" id="anexo" class="form-control-file @error('anexo[]') is-invalid @enderror" multiple>
                            @error('anexo[]')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <br>
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                    </div>
                    <br>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $('#ano').mask('0000');

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
        dd = '0' + dd;
        }

        if (mm < 10) {
        mm = '0' + mm;
        }

        today = yyyy + '-' + mm + '-' + dd;
        $('.dataFormat').attr('max', today);

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
