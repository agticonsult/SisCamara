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

<div class="card" style="background-color:white">

    <div class="card-header">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Cadastro de Ato</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('reparticao.store') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                {{-- <h3>Texto</h3>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Título</label>
                        <input type="text" class="form-control" name="titulo">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">Subtítulo</label>
                        <input type="text" class="form-control" name="subtitulo">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Corpo do Texto</label>
                        <textarea name="corpo_texto" cols="10" rows="10" class="form-control"></textarea>
                    </div>
                </div>

                <br><hr>

                <h3>Dados Gerais</h3>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Classificação do Ato</label>
                        <select name="id_classificacao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($classificacaos as $classificacao)
                                <option value="{{ $classificacao->id }}">{{ $classificacao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Ano</label>
                        <input type="text" class="form-control" name="ano" id="ano">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Número</label>
                        <input type="text" class="form-control" name="numero">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Grupo</label>
                        <select name="id_grupo" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Tipo de Ato</label>
                        <select name="id_tipo_ato" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_atos as $tipo_ato)
                                <option value="{{ $tipo_ato->id }}">{{ $tipo_ato->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Assunto</label>
                        <select name="id_assunto" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($assuntos as $assunto)
                                <option value="{{ $assunto->id }}">{{ $assunto->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Órgão que editou o ato</label>
                        <select name="id_orgao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($orgaos as $orgao)
                                <option value="{{ $orgao->id }}">{{ $orgao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Forma de Publicação</label>
                        <select name="id_forma_publicacao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($forma_publicacaos as $forma_publicacao)
                                <option value="{{ $forma_publicacao->id }}">{{ $forma_publicacao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Data de Publicação</label>
                        <input type="date" class="form-control" name="data_publicacao">
                    </div>
                </div>
                <div class="form-check col-md-6">
                    <input type="checkbox" class="form-check-input" id="altera_dispositivo" name="altera_dispositivo">
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
                        <li>Áudio (mp3)</li>
                        <li>Vídeo (mp4, mkv)</li>
                    </ul>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="anexo">Arquivo</label>
                        <input type="file" name="anexo[]" id="anexo" class="form-control-file" multiple>
                    </div>
                </div> --}}

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
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

    $("#form").validate({
        rules : {
            // Texto
            titulo:{
                required:true
            },
            corpo_texto:{
                required:true
            },

            // Dados Gerais
            id_classificacao:{
                required:true
            },
            ano:{
                required:true
            },
            numero:{
                required:true
            },
            id_grupo:{
                required:true
            },
            id_tipo_ato:{
                required:true
            },
            id_assunto:{
                required:true
            },
            id_orgao:{
                required:true
            },
        },
        messages:{
            // Texto
            titulo:{
                required:"Campo obrigatório"
            },
            corpo_texto:{
                required:"Campo obrigatório"
            },

            // Dados Gerais
            id_classificacao:{
                required:"Campo obrigatório"
            },
            ano:{
                required:"Campo obrigatório"
            },
            numero:{
                required:"Campo obrigatório"
            },
            id_grupo:{
                required:"Campo obrigatório"
            },
            id_tipo_ato:{
                required:"Campo obrigatório"
            },
            id_assunto:{
                required:"Campo obrigatório"
            },
            id_orgao:{
                required:"Campo obrigatório"
            },
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
