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

<h1 class="h3 mb-3">Edição de documento</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="#" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Título</label>
                        <input type="text" class="form-control" placeholder="Título do documento" value="{{ $departamentoDocumentoEdit->titulo }}" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Tipo de Documento</label>
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
                        <textarea name="conteudo" class="form-control" cols="30" rows="20" id="conteudo">{{ $departamentoDocumentoEdit->conteudo }}</textarea>
                    </div>
                </div>
                {{-- <br>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Status documento</label>
                        <select name="id_status" id="id_status" class="form-control @error('id_status') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($statusDepDocs as $status)
                                <option value="{{ $status->id }}" {{ $historicoMovimentacao->id_status == $status->id ? 'selected' : ''}}> {{ $status->descricao }} </option>
                            @endforeach
                        </select>
                        @error('id_status')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div> --}}
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        <a href="{{ route('departamento_documento.index') }}" class="btn btn-light">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
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
        readonly: true,
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        entity_encoding : "raw",
        force_br_newlines : false,
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        spellchecker_language: 'br',
        language: 'pt_BR',
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
