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

<h1 class="h3 mb-3">Cadastro de documento</h1>
@if (!isset($tipoDocumentos[0]))
    <div class="alert alert-warning alert-dismissible" role="alert">
        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
        <div class="alert-message">
            <strong>Sem TIPO DE DOCUMENTO cadastrado!</strong>
            <a href="{{ route('configuracao.tipo_documento.create') }}">Clique aqui para cadastrar</a>
        </div>
    </div>
@endif
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('departamento_documento.store') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Título</label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" name="titulo" placeholder="Título do documento" value="{{ old('titulo') }}">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Workflow de tramitação</label>
                        <select name="id_tipo_workflow" id="id_tipo_workflow" class="form-control @error('id_tipo_workflow') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_workflows as $tw)
                                <option value="{{ $tw->id }}">{{ $tw->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_workflow')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Tipo de Documento</label>
                        <select name="id_tipo_documento" id="id_tipo_documento" class="select2 form-control @error('id_tipo_documento') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipoDocumentos as $tipoDocumento)
                                <option value="{{ $tipoDocumento->id }}">{{ $tipoDocumento->nome }} - Nível: {{ $tipoDocumento->nivel }}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_documento')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-4" style="display: none;" id="dep">
                        <label class="form-label">*Selecione o primeiro departamento</label>
                        <select name="id_departamento" id="id_departamento" class="select2 form-control @error('id_departamento') is-invalid @enderror">
                            <option value="" selected disabled>-- Selecione --</option>
                        </select>
                        @error('id_departamento')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="body">Conteúdo</label>
                        <textarea name="conteudo" class="form-control @error('conteudo') is-invalid @enderror" cols="30" rows="20" id="conteudo">{{ old("conteudo") }}</textarea>
                        @error('conteudo')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>
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

        $('#id_tipo_workflow').on('change', function() {
            var selected = $(this).val();

            if (selected == 2) {
                $('#dep').show(300);
            }
            else{
                $('#dep').hide(300);
            }
        });

        $('#id_tipo_documento').on('change', function(e){
            var id_tipo_documento = $('#id_tipo_documento').select2("val");
            console.log(id_tipo_documento);
            var verifica = true;

            $.get("{{ route('departamento_documento.getDepartamentos', '') }}" + "/" + id_tipo_documento, function(departamentos){
                $('select[name=id_departamento]').empty();
                $.each(departamentos, function(key, value) {
                    if (verifica) {
                        $('select[name=id_departamento]').append('<option value="" selected disabled> Selecione o departamento</option>');
                    }
                    verifica = false;
                    $('select[name=id_departamento]').append('<option value=' + value.id + '>' + value.descricao + '</option>');
                });
            });
        });

    });

</script>

@endsection
