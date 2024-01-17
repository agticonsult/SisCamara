@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
    input[type='file'] {
        display: none;
    }
    /* .max-width {
        max-width: 500px;
        width: 100%;
    } */
    #imgPhoto {
        margin-top: 10%;
        /* width: 100%;
        height: 100%; */
        /* padding:10px; */
        background-color: #eee;
        border: 5px solid #ccc;
        border-radius: 50%;
        cursor: pointer;
        transition: background .3s;
    }
    #imgPhoto:hover{
        background-color: rgb(180, 180, 180);
        border: 5px solid #111;
    }
</style>
@include('errors.alerts')
@include('errors.errors')

<h1 class="h3 mb-3">Cadastro de Agente Político</h1>
<div class="card" style="background-color:white">

    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="background-color: rgb(196, 216, 238)">
                        <h5 class="card-title">Cadastrar</h5>
                        <p class="card-text">Realizar um novo cadastro no sistema</p>
                        <a href="{{ route('agente_politico.novo_agente_politico') }}" class="btn btn-primary">Avançar</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="background-color: rgb(196, 202, 209)">
                        <h5 class="card-title">Vincular a um usuário existente</h5>
                        <p class="card-text">Vincular a um usuário existente no sistema</p>
                        <a href="{{ route('agente_politico.vincularUsuario') }}" class="btn btn-secondary">Avançar</a>
                    </div>
                </div>
            </div>
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
    $('#cep').mask('00.000-000');

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

    //código referente a foto de perfil
    let photo = document.getElementById('imgPhoto');
    let file = document.getElementById('flImage');

    photo.addEventListener('click', () => {
        file.click();
    });

    file.addEventListener('change', () => {

        if (file.files.length <= 0) {
            return;
        }

        let reader = new FileReader();

        reader.onload = () => {
            photo.src = reader.result;
        }

        reader.readAsDataURL(file.files[0]);
    });

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
                url: "{{ route('processo_legislativo.pleito_eleitoral.get', '') }}"  + "/" + id_pleito_eleitoral,
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
                    $('#nome').attr('required', true);
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
                    $('#nome').attr('required', false);
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
