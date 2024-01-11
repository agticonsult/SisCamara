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
            <strong>Cadastro de Usuário</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('usuario.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">*Nome</label>
                                <input class="form-control" type="text" name="nome" id="nome" placeholder="Informe o nome" value="{{ old('nome') }}">
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Perfil</label>
                                <select name="id_perfil[]" class="form-control select2" multiple>
                                    @foreach ($perfils as $pf)
                                        <option value="{{ $pf->id }}">
                                            {{ $pf->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

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
            nome:{
                required:true
            },
            cpf:{
                required:true
            },
            dt_nascimento_fundacao:{
                required:true
            },
            email:{
                required:true
            },
            password:{
                required:true,
                minlength:6,
                maxlength:35
            },
            confirmacao:{
                required:true,
                minlength:6,
                maxlength:35
            },
            perfil:{
                required:true
            }
        },
        messages:{
            nome:{
                required:"Campo obrigatório"
            },
            cpf:{
                required:"Campo obrigatório"
            },
            dt_nascimento_fundacao:{
                required:"Campo obrigatório",
                min:"Data mínima: 01/01/1899",
                max:"Data máxima: data de hoje",
            },
            email:{
                required:"Campo obrigatório"
            },
            password:{
                required:"Campo obrigatório",
                minlength:"Minímo 6 caracteres",
                maxlength:"Máximo 35 caracteres"
            },
            confirmacao:{
                required:"Campo obrigatório",
                minlength:"Minímo 6 caracteres",
                maxlength:"Máximo 35 caracteres"
            },
            perfil:{
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
