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
            <form action="{{ route('ato.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <h3>Dados Gerais</h3>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Título</label>
                        <input type="text" class="form-control" name="titulo">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Ano</label>
                        <input type="text" class="form-control" name="ano">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Número</label>
                        <input type="text" class="form-control" name="numero">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Grupo</label>
                        <select name="id_grupo" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Tipo de Ato</label>
                        <select name="tipo_ato" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_atos as $tipo_ato)
                                <option value="{{ $tipo_ato->id }}">{{ $tipo_ato->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <br><hr>
                <h3>Texto</h3>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Subtítulo</label>
                        <textarea name="subtitulo" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Corpo do Texto</label>
                        <textarea name="corpo_texto" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                </div>

                {{-- <h5>Dados Pessoais</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">*Nome</label>
                                <input class="form-control" type="text" name="nomeCompleto" id="nomeCompleto" placeholder="Informe o nome" value="{{ old('nomeCompleto') }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*CPF</label>
                                <input class="cpf form-control" type="text" name="cpf" id="cpf" placeholder="Informe o CPF" value="{{ old('cpf') }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Data de Nascimento</label>
                                <input class="dataFormat form-control" type="date" min="1899-01-01" max="2000-13-13" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ old('dt_nascimento_fundacao') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">*Email</label>
                                <input class="form-control" type="email" name="email" placeholder="Informe um email válido" value="{{ old('email') }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                <input class="form-control" type="password" name="password" placeholder="Informe uma senha" value="{{ old('password') }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Confirme a senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                <input class="form-control" type="password" name="confirmacao" placeholder="Confirme a senha" value="{{ old('confirmacao') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <br><hr><br>
                <h5>Dados Gerais</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Tipo de Perfil</label>
                                <select name="tipo_perfil[]" id="tipo_perfil" class="form-control select2" multiple required>
                                    <option value="2">Funcionário</option>
                                    <option value="3">Cliente</option>
                                    <option value="1">Administrador</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="lotacao">*Lotação</label>
                                <select name="lotacao" id="lotacao" class="form-control select2">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($municipios as $municipio)
                                        <option value="{{ $municipio->id }}">{{ $municipio->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="d-none form-group col-md-6" id="funcionario">
                                <label class="form-label">*Perfil Funcionário</label>
                                <select name="id_perfil_funcionario[]" class="form-control select2" multiple>
                                    @foreach ($perfil_funcionarios as $pf)
                                        <option value="{{ $pf->id }}">
                                            {{ $pf->descricao }} -
                                            {{ $pf->id_abrangencia != null ? $pf->abrangencia->descricao : 'abrangência não informada' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-none form-group col-md-6" id="cliente">
                                <label class="form-label">*Perfil Cliente</label>
                                <select name="id_perfil_cliente[]" class="form-control select2" multiple>
                                    @foreach ($perfil_clientes as $pc)
                                        <option value="{{ $pc->id }}">
                                            {{ $pc->descricao }} -
                                            {{ $pc->id_abrangencia != null ? $pc->abrangencia->descricao : 'abrangência não informada' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-none form-group col-md-6" id="administrador">
                                <label class="form-label">*Perfil Administrador</label>
                                <select name="id_perfil_adm[]" class="form-control select2" multiple>
                                    @foreach ($perfil_adms as $pa)
                                        <option value="{{ $pa->id }}">
                                            {{ $pa->descricao }} -
                                            {{ $pa->id_abrangencia != null ? $pa->abrangencia->descricao : 'abrangência não informada' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div> --}}

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
    $('#cep').mask('00.000-000');
    $('.cpf').mask('000.000.000-00');

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
            titulo:{
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
            tipo_ato:{
                required:true
            },
            subtitulo:{
                required:true
            },
            corpo_texto:{
                required:true
            }
        },
        messages:{
            titulo:{
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
            tipo_ato:{
                required:"Campo obrigatório"
            },
            subtitulo:{
                required:"Campo obrigatório"
            },
            corpo_texto:{
                required:"Campo obrigatório"
            }
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
