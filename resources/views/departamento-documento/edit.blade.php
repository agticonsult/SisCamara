@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')

<h1 class="h3 mb-3">Dados do documento</h1>
<div class="card" style="background-color:white">

    <div id="accordion1">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Dados do documento
                    </button>
                </h5>
            </div>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion1">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" placeholder="Título do documento" value="{{ $departamentoDocumentoEdit->titulo }}" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">Tipo de Documento</label>
                                <select name="id_tipo_documento" id="id_tipo_documento" class="select2 form-control" disabled>
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($tipoDocumentos as $tipoDocumento)
                                        <option value="{{ $tipoDocumento->id }}" {{ $departamentoDocumentoEdit->id_tipo_documento == $tipoDocumento->id ? 'selected' : '' }}>{{ $tipoDocumento->nome }} - Nível: {{ $tipoDocumento->nivel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label" for="body">Conteúdo</label>
                                <textarea name="conteudo" class="form-control" cols="30" rows="30" id="conteudo">{{ $departamentoDocumentoEdit->conteudo }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="accordion2">
        <div class="card">
            <div class="card-header" id="heading2">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                        Departamento(s) vinculado(s) ao documento
                    </button>
                </h5>
            </div>
            <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion2">
                <div class="card-body">
                    <div class="col-md-12">
                        <strong><h4>Departamento(s)</h4></strong>
                        @foreach ($departamentoTramitacao as $tramitacao)
                            <ul>
                                <li>
                                    <h5>
                                        {{ $tramitacao->departamento->descricao }}
                                    </h5>
                                </li>
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="accordion3">
        <div class="card">
            <div class="card-header" id="heading3">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                        Aprovar e encaminhar ao próximo departamento
                    </button>
                </h5>
            </div>
            <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion3">
                <div class="card-body">
                    <form action="{{ route('departamento_documento.update', $departamentoDocumentoEdit->id) }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Status</label>
                                    <select name="id_status" id="id_status" class="form-control @error('id_status') is-invalid @enderror">
                                        <option value="" selected disabled>--Selecione--</option>
                                        @foreach ($statusDepDocs as $status)
                                            <option value="{{ $status->id }}">{{ $status->descricao }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_status')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Próximo departamento na tramitação do documento</label>
                                    <input type="text" class="form-control" value="{{ $proximoDep->departamento->descricao }}" readonly>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <a href="{{ route('departamento_documento.index') }}" class="btn btn-light">Voltar</a>
    </div>

</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="https://cdn.tiny.cloud/1/hh6dctatzptohe71nfevw76few6kevzc4i1q1utarze7tude/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    tinymce.init({
        selector: 'textarea',
        readonly: true, // Define o editor como somente leitura (modo de visualização)
        menubar: false, // Esconde a barra de ferramentas
        toolbar: false, // Desativa a barra de ferramentas
        setup: function(editor) {
            editor.on('init', function() {
                editor.getBody().setAttribute('contenteditable', false); // Torna o conteúdo do editor não editável
            });
        }
    });

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
