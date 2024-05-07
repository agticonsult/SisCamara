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

<h1 class="h3 mb-3">Cadastro de Usuário</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('usuario.storePessoaFisica') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">*Nome</label>
                                <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe o nome" value="{{ old('nome') }}">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*CPF</label>
                                <input class="cpf form-control @error('cpf') is-invalid @enderror" type="text" name="cpf" id="cpf" placeholder="Informe o CPF" value="{{ old('cpf') }}">
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Data de Nascimento</label>
                                <input class="dataFormat form-control @error('dt_nascimento_fundacao') is-invalid @enderror" type="date" min="1899-01-01" max="2000-13-13" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ old('dt_nascimento_fundacao') }}">
                                @error('dt_nascimento_fundacao')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Perfil</label>
                                <select name="id_perfil[]" class="form-control select2 @error('id_perfil') is-invalid @enderror" multiple>
                                    @foreach ($perfils as $pf)
                                        <option value="{{ $pf->id }}">
                                            {{ $pf->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_perfil')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label">*Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Informe um email válido" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="Informe uma senha">
                                <br>
                                <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                                <label for="showPassword">Mostrar Senha</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Confirme a senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                <input class="form-control @error('confirmacao') is-invalid @enderror" type="password" name="confirmacao" id="confirmacao" placeholder="Confirme a senha">
                                @error('confirmacao')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        <a href="{{ route('usuario.index') }}" class="btn btn-secondary">Voltar</a>
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

    function togglePasswordVisibility() {
        var passwordInput = document.getElementById('password');
        var showPasswordCheckbox = document.getElementById('showPassword');

        if (showPasswordCheckbox.checked) {
            passwordInput.type = 'text';
        }
        else {
            passwordInput.type = 'password';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        var confirmationInput = document.getElementById("confirmacao");

        confirmationInput.addEventListener("paste", function (e) {
            e.preventDefault();
            alert("Ação de colar não permitida neste campo de confirmação de senha!");
        });
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
