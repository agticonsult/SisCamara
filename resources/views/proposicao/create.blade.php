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
                <input type="text" class="form-control" name="texto_proposicao" id="texto_proposicao" value="{{ old("texto_proposicao") }}" hidden>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1" id="salvar">Salvar</button>
                    <a href="{{ route('proposicao.index') }}" class="btn btn-light m-1">Voltar</a>
                </div>
                <br>
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

        $('#salvar').on('click', function(){
            var plainText = tinymce.activeEditor.getBody().textContent;
            $('#texto_proposicao').val(plainText);
        });

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
                        tinymce.get('conteudo').setContent(resposta.data.conteudo);
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
