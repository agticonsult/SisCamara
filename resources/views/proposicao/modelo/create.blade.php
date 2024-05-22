@extends('layout.main')

@section('content')
    @include('errors.alerts')

    <h1 class="h3 mb-3">Cadastro de Modelo</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('proposicao.modelo.store') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="assunto">Assunto</label>
                            <input type="text" class="form-control @error('assunto') is-invalid @enderror" name="assunto" value="{{ old("assunto") }}">
                            @error('assunto')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="body">Conteúdo</label>
                            <textarea name="conteudo" class="form-control @error('conteudo') is-invalid @enderror" cols="30" rows="15" id="conteudo">{{ old("conteudo") }}</textarea>
                            @error('conteudo')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                            <a href="{{ route('proposicao.modelo.index') }}" class="btn btn-light m-1">Voltar</a>
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
    </script>
@endsection
