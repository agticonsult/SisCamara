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

<h1 class="h3 mb-3">Alteração de Agente Político</h1>
<div class="card" style="background-color:white">

    {{-- <div class="card-header">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Alteração de Agente Político</strong>
        </h2>
    </div> --}}

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('agente_politico.update', $agente_politico->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Pleito Eleitoral</label>
                        <select name="id_pleito_eleitoral" id="id_pleito_eleitoral" class="select2 form-control" required>
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($pleito_eleitorals as $pleito_eleitoral)
                                <option value="{{ $pleito_eleitoral->id }}" {{ $agente_politico->id_pleito_eleitoral == $pleito_eleitoral->id ? 'selected' : ''}}>
                                    {{ $pleito_eleitoral->ano_pleito }} -
                                    Mandato <strong>{{ date('d/m/Y', strtotime($agente_politico->dataInicioMandato)) }}</strong> - <strong>{{ date('d/m/Y', strtotime($agente_politico->dataFimMandato))}}</strong>
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Cargo Eletivo</label>
                        <select name="id_cargo_eletivo" id="id_cargo_eletivo" class="select2 form-control" required>
                            <option value="" selected disabled>--Selecione--</option>
                            @for ($i=0; $i<Count($cargos_eletivos); $i++)
                                <option value="{{ $cargos_eletivos[$i]['id'] }}" {{ $agente_politico->id_cargo_eletivo == $cargos_eletivos[$i]['id'] ? 'selected' : ''}}>
                                    {{ $cargos_eletivos[$i]['descricao'] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data início mandato</label>
                        <input type="date" name="dataInicioMandato" class="form-control" value="{{ $agente_politico->dataInicioMandato }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data fim mandato</label>
                        <input type="date" name="dataFimMandato" class="form-control" value="{{ $agente_politico->dataFimMandato }}" required>
                    </div>
                </div>
                <br><hr>
                <div class="row">
                    <div class="col-md-3 mr-3">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h4 class="mb-3">Detalhes do Perfil</h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="max-width">
                                    <div class="imageContainer">
                                        {{-- <span>Clique na imagem para alterar, depois clique em salvar</span> --}}
                                        @if ($temFoto == 1)
                                            @php
                                                $path = storage_path('app/public/foto-perfil/'.$foto_perfil->nome_hash);
                                                // $path = public_path('foto-perfil/'.$foto_perfil->nome_hash);
                                                if (File::exists($path)){
                                                    $base64 = base64_encode(file_get_contents($path));
                                                    $src = 'data:image/png;base64,' . $base64;
                                                }
                                            @endphp
                                            @if (isset($src))
                                                <img src="{{$src}}" class="img-fluid rounded-circle mb-2" width="60%" height="60%" alt="Selecione uma imagem" id="imgPhoto">
                                            @else
                                                <img src="{{ asset('img/user-avatar2.png') }}" class="img-fluid rounded-circle mb-2" width="60%" height="60%" alt="Selecione uma imagem" id="imgPhoto">
                                            @endif
                                        @else
                                            <img src="{{ asset('img/user-avatar2.png') }}" class="img-fluid rounded-circle mb-2" width="60%" height="60%" alt="Selecione uma imagem" id="imgPhoto">
                                        @endif
                                            <input type="file" id="flImage" name="fImage" accept="image/jpg, image/jpeg, image/png" value="{{'fImage'}}">
                                    </div>
                                    <span>(tamanho máximo da imagem: {{ $filesize->mb }}MB)</span>

                                </div>
                                {{-- <div class="mt-2">
                                    <button type="submit" class="button_submit btn btn-primary"><i class="align-middle me-2 fas fa-fw fa-upload"></i> Salvar</button>
                                </div> --}}
                                <br>
                                <div class="cpf text-muted mb-2">{{ $agente_politico->usuario->cpf }}</div>
                                <h4 class="mb-2 underline"><strong>{{ $agente_politico->usuario->pessoa->nome }}</strong></h4>
                                <h4 class="mb-0">{{ $agente_politico->usuario->email }}</h4>
                                {{-- <div class="mt-5">
                                    <ul class="navbar-nav" style="text-align: center">
                                        <h4 style="text-align: center">
                                            Perfis: &nbsp
                                        </h4>
                                        <h5>
                                            @foreach (Auth::user()->permissoes_ativas as $pa)
                                                <li>
                                                    {{ $pa->perfil->descricao }}
                                                </li>
                                            @endforeach
                                        </h5>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-left mb-0 mt-2">Atualizar dados</h4>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-0">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Dados Pessoais</h5>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">*Nome</label>
                                                    <input class="form-control" type="text" name="nome" id="nome" placeholder="Informe seu nome" value="{{ $agente_politico->usuario->pessoa->nome != null ? $agente_politico->usuario->pessoa->nome : old('nome') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Apelido</label>
                                                    <input class="form-control" type="text" name="apelidoFantasia" id="apelidoFantasia" placeholder="Apelido" value="{{ $agente_politico->usuario->pessoa->apelidoFantasia != null ? $agente_politico->usuario->pessoa->apelidoFantasia : old('apelidoFantasia') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">*CPF</label>
                                                    <input class="cpf form-control" type="text" name="cpf" id="cpf" placeholder="Informe seu CPF" value="{{ $agente_politico->usuario->cpf != null ? $agente_politico->usuario->cpf: old('cpf') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">*Data de Nascimento</label>
                                                    <input class="dataFormat form-control" type="date" min="1899-01-01" max="2000-13-13" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ $agente_politico->usuario->pessoa->dt_nascimento_fundacao != null ? $agente_politico->usuario->pessoa->dt_nascimento_fundacao : old('dt_nascimento_fundacao') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">*Email</label>
                                                    <input class="form-control" type="email" name="email" placeholder="Informe um email válido" value="{{ $agente_politico->usuario->email != null ? $agente_politico->usuario->email : old('email') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Celular/Telefone</label>
                                                    <input class="telefone form-control" type="text"  name="telefone_celular" value="{{ $agente_politico->usuario->telefone_celular != null ? $agente_politico->usuario->telefone_celular : old('telefone_celular') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Celular/Telefone Recado</label>
                                                    <input class="telefone form-control" type="text" name="telefone_celular2" value="{{ $agente_politico->usuario->telefone_celular2 != null ? $agente_politico->usuario->telefone_celular2 : old('telefone_celular2') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Endereço</h5>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="cep">CEP</label>
                                                    <input type="text" name="cep" id="cep" class="form-control" placeholder="Informe o CEP" value="{{ $agente_politico->usuario->pessoa->cep != null ? $agente_politico->usuario->pessoa->cep : old('cep') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="endereco">Endereço (Rua/Avenida)</label>
                                                    <input type="text" name="endereco" id="endereco" class="form-control" placeholder="Informe o endereço" value="{{ $agente_politico->usuario->pessoa->endereco != null ? $agente_politico->usuario->pessoa->endereco : old('endereco') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="numero">Número</label>
                                                    <input type="text" name="numero" id="numero" class="form-control" placeholder="Informe o número" value="{{ $agente_politico->usuario->pessoa->numero != null ? $agente_politico->usuario->pessoa->numero : old('numero') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="bairro">Bairro / Comunidade</label>
                                                    <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Informe o bairro" value="{{ $agente_politico->usuario->pessoa->bairro != null ? $agente_politico->usuario->pessoa->bairro : old('bairro') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="complemento">Complemento</label>
                                                    <input type="text" name="complemento" id="complemento" class="form-control" placeholder="Informe o complemento" value="{{ $agente_politico->usuario->pessoa->complemento != null ? $agente_politico->usuario->pessoa->complemento : old('complemento') }}">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="ponto_referencia">Ponto de Referência</label>
                                                    <input type="text" name="ponto_referencia" class="form-control" placeholder="Informe o ponto de referência" value="{{ $agente_politico->usuario->pessoa->ponto_referencia != null ? $agente_politico->usuario->pessoa->ponto_referencia : old('ponto_referencia') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-md-12">
                                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                                        <a href="{{ route('agente_politico.index') }}" class="btn btn-light m-1">Voltar</a>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label">*Nome</label>
                        <input class="form-control" type="text" name="nome" id="nome" placeholder="Informe seu nome" value="{{ $agente_politico->usuario->pessoa->nome }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">Apelido</label>
                        <input class="form-control" type="text" name="apelidoFantasia" id="apelidoFantasia" placeholder="Apelido" value="{{ $agente_politico->usuario->pessoa->apelidoFantasia }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*CPF</label>
                        <input class="cpf form-control" type="text" name="cpf" id="cpf" placeholder="Informe seu CPF" value="{{ $agente_politico->usuario->cpf }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data de Nascimento</label>
                        <input class="dataFormat form-control" type="date" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ $agente_politico->usuario->pessoa->dt_nascimento_fundacao }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Email</label>
                        <input class="form-control" type="email" name="email" placeholder="Informe um email válido" value="{{ $agente_politico->usuario->email }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">Celular/Telefone</label>
                        <input class="telefone form-control" type="text"  name="telefone_celular" value="{{ $agente_politico->usuario->telefone_celular }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">Celular/Telefone Recado</label>
                        <input class="telefone form-control" type="text" name="telefone_celular2" value="{{ $agente_politico->usuario->telefone_celular2 }}">
                    </div>
                </div>
                <br><hr>
                <h5>Endereço</h5>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="cep">CEP</label>
                        <input type="text" name="cep" id="cep" class="form-control" placeholder="Informe o CEP" value="{{ $agente_politico->usuario->pessoa->cep }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="endereco">Endereço (Rua/Avenida)</label>
                        <input type="text" name="endereco" id="endereco" class="form-control" placeholder="Informe o endereço" value="{{ $agente_politico->usuario->pessoa->endereco }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="numero">Número</label>
                        <input type="text" name="numero" id="numero" class="form-control" placeholder="Informe o número" value="{{ $agente_politico->usuario->pessoa->numero }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="bairro">Bairro</label>
                        <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Informe o bairro" value="{{ $agente_politico->usuario->pessoa->bairro }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="complemento">Complemento</label>
                        <input type="text" name="complemento" id="complemento" class="form-control" placeholder="Informe o complemento" value="{{ $agente_politico->usuario->pessoa->complemento }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ponto_referencia">Ponto de Referência</label>
                        <input type="text" name="ponto_referencia" class="form-control" placeholder="Informe o ponto de referência" value="{{ $agente_politico->usuario->pessoa->ponto_referencia }}">
                    </div>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                    <a href="{{ route('agente_politico.index') }}" class="btn btn-light m-1">Voltar</a>
                </div> --}}

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
    $('.cpf').mask('000.000.000-00');
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

    $("#form").validate({
        rules : {
            id_pleito_eleitoral:{
                required:true
            },
            id_cargo_eletivo:{
                required:true
            },
            dataInicioMandato:{
                required:true
            },
            dataFimMandato:{
                required:true
            },
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
        },
        messages:{
            id_pleito_eleitoral:{
                required:"Campo obrigatório"
            },
            id_cargo_eletivo:{
                required:"Campo obrigatório"
            },
            dataInicioMandato:{
                required:"Campo obrigatório"
            },
            dataFimMandato:{
                required:"Campo obrigatório"
            },
            nome:{
                required:"Campo obrigatório"
            },
            cpf:{
                required:"Campo obrigatório"
            },
            dt_nascimento_fundacao:{
                required:"Campo obrigatório"
            },
            email:{
                required:"Campo obrigatório"
            },
        }
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

        $('#datatables-reponsive').dataTable({
            "oLanguage": {
                "sLengthMenu": "Mostrar _MENU_ registros por página",
                "sZeroRecords": "Nenhum registro encontrado",
                "sInfo": "Mostrando _START_ / _END_ de _TOTAL_ registro(s)",
                "sInfoEmpty": "Mostrando 0 / 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros)",
                "sSearch": "Pesquisar: ",
                "oPaginate": {
                    "sFirst": "Início",
                    "sPrevious": "Anterior",
                    "sNext": "Próximo",
                    "sLast": "Último"
                }
            },
        });

    });

</script>

@endsection
