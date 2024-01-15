@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAUgxBPrGkKz6xNwW6Z1rJh26AqR8ct37A"></script>
<script src="{{ asset('js/gmaps.js') }}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')
@include('errors.errors')

<h1 class="h3 mb-3">Alteração de Proposição</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('proposicao.update', $proposicao->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="titulo">*Título</label>
                        <input type="text" class="form-control" name="titulo" value="{{ $proposicao->titulo }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Modelo</label>
                        <select name="id_modelo" id="id_modelo" class="select2 form-control">
                            <option value="{{ $proposicao->id_modelo }}" selected>{{ $proposicao->modelo->assunto }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="id_localizacao">*Localização</label>
                        <select name="id_localizacao" id="id_localizacao" class="select2 form-control">
                            @foreach ($localizacaos as $localizacao)
                                <option value="{{ $localizacao->id }}" {{ $localizacao->id == $proposicao->id_localizacao ? 'selected' : '' }}>{{ $localizacao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Status</label>
                        <select name="id_status" id="id_status" class="select2 form-control">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ $status->id == $proposicao->id_status ? 'selected' : '' }}>{{ $status->descricao }}</option>
                            @endforeach
                        </select>
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
                                            <input type="text" class="form-control" name="assunto" id="assunto" value="{{ $proposicao->assunto }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="colored-icon-4" role="tabpanel">
                                    <textarea name="conteudo" id="conteudo" class="form-control" cols="30" rows="10">{{ $proposicao->conteudo }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
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
    $("#form").validate({
        rules : {
            titulo:{
                required:true
            },
            id_modelo:{
                required:true
            },
            assunto:{
                required:true
            },
            conteudo:{
                required:true
            },
            id_localizacao:{
                required:true
            },
            id_status:{
                required:true
            },
        },
        messages:{
            titulo:{
                required:"Campo obrigatório"
            },
            id_modelo:{
                required:"Campo obrigatório"
            },
            assunto:{
                required:"Campo obrigatório"
            },
            conteudo:{
                required:"Campo obrigatório"
            },
            id_localizacao:{
                required:"Campo obrigatório"
            },
            id_status:{
                required:"Campo obrigatório"
            },
        }
    });

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

    });

</script>

@endsection
