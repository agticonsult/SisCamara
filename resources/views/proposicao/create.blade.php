@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <style>
        .form-control:focus {
            background-color: #ffffff !important; /* Altere a cor de fundo conforme necessário */
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .trumbowyg-box, .trumbowyg-editor {
            border-color: #ced4da; /* Mesma cor da borda do campo de input */
            background-color: #ffffff; /* Fundo branco para o editor */
            color: #000000; /* Cor do texto para preto */
        }
        .trumbowyg-box.trumbowyg-editor-focused, .trumbowyg-editor:focus {
            background-color: #ffffff !important; /* Mesma cor de fundo do campo de input */
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }
    </style>

    <h1 class="h3 mb-3">Cadastro de Proposição</h1>
    @if (!isset($modelos[0]))
        <div class="alert alert-warning alert-dismissible" role="alert">
            {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
            <div class="alert-message">
                <strong>Sem MODELO DE PROPOSIÇÃO cadastrado!</strong>
                <a href="{{ route('proposicao.modelo.create') }}">Clique aqui para cadastrar</a>
            </div>
        </div>
    @endif

    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('proposicao.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="titulo">*Título</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" name="titulo" value="{{ old("titulo") }}">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Modelo</label>
                            <select name="id_modelo" id="id_modelo" class="select2 form-control @error('id_modelo') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->id }}">{{ $modelo->assunto }}</option>
                                @endforeach
                            </select>
                            @error('id_modelo')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row" id="assunto_conteudo">
                        <div class="col-md-12">
                            <div class="tab tab-primary">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#colored-icon-3" id="selecionar_assunto" data-toggle="tab" role="tab">
                                            Assunto
                                        </a>
                                    </li>
                                    <li class="nav-item" id="conteudo_clicado">
                                        <a class="nav-link" href="#colored-icon-4" id="selecionar_conteudo" data-toggle="tab" role="tab">
                                            Conteúdo
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="colored-icon-3" role="tabpanel">
                                        <div class="">
                                            <div class="col-md-6 mb-3">
                                                <input type="text" class="form-control @error('assunto') is-invalid @enderror" name="assunto" id="assunto">
                                                @error('assunto')
                                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="colored-icon-4" role="tabpanel">
                                        <textarea name="conteudo" id="conteudo" class="form-control @error('conteudo') is-invalid @enderror" cols="30" rows="10"></textarea>
                                        @error('conteudo')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <input type="text" class="form-control" name="texto_proposicao" id="texto_proposicao" value="{{ old("texto_proposicao") }}" hidden> --}}
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary m-1" id="salvar">Salvar</button>
                            <a href="{{ route('proposicao.index') }}" class="btn btn-light m-1">Voltar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#conteudo').trumbowyg({
            lang: 'pt_br',
            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['unorderedList', 'orderedList'],
                ['table', 'tableCellBackgroundColor', 'tableBorderColor'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['fontfamily'],
                ['fontsize'],
                ['horizontalRule'],
                ['link'],
                ['foreColor', 'backColor'],
            ],
            plugins: {
                fontfamily: {
                    fonts: [
                        "Arial",
                        "Arial Black",
                        "Comic Sans MS",
                        "Courier New",
                        "Lucida Console",
                        "Tahoma",
                        "Times New Roman",
                        "Verdana"
                    ]
                },
                fontsize: {
                    sizeList: [
                        '12px',
                        '14px',
                        '16px'
                    ]
                },
                table: {
                    styler: 'table',
                },
                tagClasses: {
                   table: 'table',
                },
            }
        });

        $(document).ready(function() {
            // $('#salvar').on('click', function(){
            //     var plainText = tinymce.activeEditor.getBody().textContent;
            //     $('#texto_proposicao').val(plainText);
            // });

            $('#id_modelo').on('change', function(e){
                var id_modelo = $(this).val();

                e.preventDefault();
                // $(this).html('Enviando..');

                $.ajax({
                    url: "{{ route('proposicao.modelo.get', '') }}"  + "/" + id_modelo,
                    type: "GET",
                    dataType: 'json',
                    success: function (resposta) {
                        if (resposta.data){
                            document.getElementById('assunto').value = resposta.data.assunto;
                            $('#conteudo').trumbowyg('html', resposta.data.conteudo);
                        }
                        else{
                            if (resposta.erro){
                                alert('Erro! Contate o administrador do sistema.');
                            }
                        }
                    },
                    error: function (resposta) {
                        tinymce.get('conteudo').setContent('');
                    }
                });
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
