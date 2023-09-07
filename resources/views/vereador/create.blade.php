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
            <strong>Cadastro de Vereador</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('vereador.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Pleito Eleitoral</label>
                        <select name="id_pleito_eleitoral" id="id_pleito_eleitoral" class="select2 form-control" required>
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($pleito_eleitorals as $pleito_eleitoral)
                                <option value="{{ $pleito_eleitoral->id }}">
                                    {{ $pleito_eleitoral->ano_pleito }} -
                                    Mandato <strong>{{ $pleito_eleitoral->inicio_mandato }}</strong>-<strong>{{ $pleito_eleitoral->fim_mandato }}</strong>
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Cargo Eletivo</label>
                        <select name="id_cargo_eletivo" id="id_cargo_eletivo" class="select2 form-control" required>
                            <option value="" selected disabled>--Selecione--</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data início mandato</label>
                        <input type="date" name="dataInicioMandato" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data fim mandato</label>
                        <input type="date" name="dataFimMandato" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="selecionar_opcao">*Cadastrar usuário ou vincular a um usuário já existente?</label>
                        <select name="selecionar_opcao" class="form-control" id="optionSelect" required>
                            <option value="" selected disabled>-- Selecione --</option>
                            <option value="1">Cadastrar usuário</option>
                            <option value="2">Vincular a usuário já existente</option>
                        </select>
                    </div>
                    <div class="d-none form-group col-md-6" id="selectUser">
                        <label for="id_usuario">*Usuário</label>
                        <select name="id_usuario" class="form-control select2">
                            <option value="" selected disabled>-- Selecione --</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->pessoa->nomeCompleto }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-none row" id="cadUser">
                    <div class="col-md-12">
                        <hr>
                        <div class="col-md-12">
                            <h5>Dados Pessoais</h5>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="form-label">*Nome</label>
                                    <input class="form-control" type="text" name="nomeCompleto" id="nomeCompleto" placeholder="Informe seu nome" value="{{ old('nomeCompleto') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Apelido</label>
                                    <input class="form-control" type="text" name="apelidoFantasia" id="apelidoFantasia" placeholder="Apelido" value="{{ old('apelidoFantasia') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*CPF</label>
                                    <input class="cpf form-control" type="text" name="cpf" id="cpf" placeholder="Informe seu CPF" value="{{ old('cpf') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Data de Nascimento</label>
                                    <input class="dataFormat form-control" type="date" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" min='1899-01-01' max='2000-13-13' value="{{ old('dt_nascimento_fundacao') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Email</label>
                                    <input class="form-control" type="email" name="email" placeholder="Informe um email válido" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Celular/Telefone</label>
                                    <input class="telefone form-control" type="text"  name="telefone_celular" value="{{ old('telefone_celular') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Celular/Telefone Recado</label>
                                    <input class="telefone form-control" type="text" name="telefone_celular2" value="{{ old('telefone_celular2') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                    <input class="form-control" type="password" name="password" id="password" placeholder="Informe uma senha" value="{{ old('password') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Confirme a senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                    <input class="form-control" type="password" name="confirmacao" id="confirmacao" placeholder="Confirme a senha" value="{{ old('confirmacao') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                            <h5>Endereço</h5>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="cep">CEP</label>
                                    <input type="text" name="cep" id="cep" class="form-control" placeholder="Informe o CEP" value="{{ old('cep') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="endereco">Endereço (Rua/Avenida)</label>
                                    <input type="text" name="endereco" id="endereco" class="form-control" placeholder="Informe o endereço" value="{{ old('endereco') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="numero">Número</label>
                                    <input type="text" name="numero" id="numero" class="form-control" placeholder="Informe o número" value="{{ old('numero') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Informe o bairro" value="{{ old('bairro') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" name="complemento" id="complemento" class="form-control" placeholder="Informe o complemento" value="{{ old('complemento') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ponto_referencia">Ponto de Referência</label>
                                    <input type="text" name="ponto_referencia" class="form-control" placeholder="Informe o ponto de referência" value="{{ old('ponto_referencia') }}">
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8-beta.17/inputmask.js" integrity="sha512-XvlcvEjR+D9tC5f13RZvNMvRrbKLyie+LRLlYz1TvTUwR1ff19aIQ0+JwK4E6DCbXm715DQiGbpNSkAAPGpd5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $('#cpf').mask('000.000.000-00');
    $('.ano').mask('0000');

    function maskInputs() {
        var input = document.getElementsByClassName('telefone')
        var im = new Inputmask(
            {
                mask: ['(99)9999-9999', '(99)99999-9999'],  keepStatic: true
            }
        )
        im.mask(input)
    }
    maskInputs();

    $('#cep').on('change', function(){
        var cep = $(this).val().replace(/[.-]/g,"");
        // console.log('CEP: ', cep);
        // console.log('Quantidade de caracteres: ', cep.length);
        if (cep.length != 8){
            $("#endereco").val('');
            $("#complemento").val('');
            $("#bairro").val('');
            // $("#cidade").val('');
            // $("#uf").val('');
            alert('CEP INVÁLIDO!');
        }
        else{
            $.ajax({
                //O campo URL diz o caminho de onde virá os dados
                //É importante concatenar o valor digitado no CEP
                url: 'https://viacep.com.br/ws/'+cep+'/json/',
                //Aqui você deve preencher o tipo de dados que será lido,
                //no caso, estamos lendo JSON.
                dataType: 'json',
                //SUCESS é referente a função que será executada caso
                //ele consiga ler a fonte de dados com sucesso.
                //O parâmetro dentro da função se refere ao nome da variável
                //que você vai dar para ler esse objeto.
                success: function(resposta){
                    //Agora basta definir os valores que você deseja preencher
                    //automaticamente nos campos acima.
                    $("#endereco").val(resposta.logradouro);
                    $("#complemento").val(resposta.complemento);
                    $("#bairro").val(resposta.bairro);
                    // $("#cidade").val(resposta.localidade);
                    // $("#uf").val(resposta.uf);
                    //Vamos incluir para que o Número seja focado automaticamente
                    //melhorando a experiência do usuário
                    if (resposta.logradouro != null && resposta.logradouro != ""){
                        $("#numero").focus();
                    }
                    else{
                        $("#endereco").focus();
                    }
                },
                error: function(resposta){
                    //Agora basta definir os valores que você deseja preencher
                    //automaticamente nos campos acima.
                    alert("Erro, CEP inválido");
                    $("#endereco").val('');
                    $("#complemento").val('');
                    $("#bairro").val('');
                    // $("#cidade").val('');
                    // $("#uf").val('');
                    //Vamos incluir para que o Número seja focado automaticamente
                    //melhorando a experiência do usuário
                    $("#cep").focus();
                },
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

        $('#id_pleito_eleitoral').on('change', function() {
            var b = true;

            var id_pleito_eleitoral = $('#id_pleito_eleitoral').select2("val");
            $.ajax({
                url: "{{ route('configuracao.pleito_eleitoral.get', '') }}"  + "/" + id_pleito_eleitoral,
                type: "GET",
                dataType: 'json',
                success: function (resposta) {
                    if (resposta.data){
                        $('select[name=id_cargo_eletivo]').empty();
                        resposta.data.forEach(cargo_eletivo => {
                            if (b){
                                $('select[name=id_cargo_eletivo]').append('<option value="" selected disabled>--Selecione--</option>');
                            }
                            b = false;
                            $('select[name=id_cargo_eletivo]').append('<option value=' + cargo_eletivo.id +
                                '>' + cargo_eletivo.descricao + '</option>');
                        });
                    }
                    else{
                        if (resposta.erro){
                            alert('Erro! Contate o administrador do sistema.');
                        }
                    }
                },
                error: function (resposta) {
                    alert('Erro! Contate o administrador do sistema.');
                }
            });
        });

        $('#optionSelect').on('change', function(){
            var selected = $(this).val();
            switch (selected) {
                case '1':
                    $('#cadUser').removeClass('d-none');
                    $('#selectUser').addClass('d-none');

                    // Campos add required
                    $('#nomeCompleto').attr('required', true);
                    $('#cpf').attr('required', true);
                    $('#dt_nascimento_fundacao').attr('required', true);
                    $('#email').attr('required', true);
                    $('#password').attr('required', true);
                    $('#confirmacao').attr('required', true);

                    // Campos add required FALSE
                    $('#id_usuario').attr('required', false);
                    break;

                case '2':
                    $('#cadUser').addClass('d-none');
                    $('#selectUser').removeClass('d-none');

                    // Campos add required
                    $('#id_usuario').attr('required', true);

                    // Campos add required FALSE
                    $('#nomeCompleto').attr('required', false);
                    $('#cpf').attr('required', false);
                    $('#dt_nascimento_fundacao').attr('required', false);
                    $('#email').attr('required', false);
                    $('#password').attr('required', false);
                    $('#confirmacao').attr('required', false);
                    break;

                default:
                    $('#cadUser').addClass('d-none');
                    $('#selectUser').addClass('d-none');
                    break;
            }
        });

    });

</script>

@endsection
