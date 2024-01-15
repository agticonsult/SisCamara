@extends('layout.main')

@section('content')

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
{{-- @include('errors.errors') --}}

<div class="row">
    <div class="col-md-3 mr-3">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-3">Detalhes do Perfil</h4>
            </div>
            <div class="card-body text-center">
                <form action="{{route('upload_foto')}}" id="form-upload" class="form_prevent_multiple_submits" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="max-width">
                        <div class="imageContainer">
                            <span>Clique na imagem para alterar, depois clique em salvar</span>
                            <span>(tamanho máximo da imagem: {{ $filesize->mb }}MB)</span>
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
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="button_submit btn btn-primary"><i class="align-middle me-2 fas fa-fw fa-upload"></i> Salvar</button>
                    </div>
                </form>
                <br>
                <div class="cpf text-muted mb-2">{{ $user->cpf }}</div>
                <h4 class="mb-2 underline"><strong>{{ $user->pessoa->nome }}</strong></h4>
                <h4 class="mb-0">{{ $user->email }}</h4>
                <div class="mt-5">
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
                </div>

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
                    <form action="{{ route('home.update', $user->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Dados Pessoais</h5>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="form-label">*Nome</label>
                                        <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe seu nome" value="{{ $user->pessoa->nome != null ? $user->pessoa->nome : old('nome') }}">
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">Apelido</label>
                                        <input class="form-control @error('apelidoFantasia') is-invalid @enderror" type="text" name="apelidoFantasia" id="apelidoFantasia" placeholder="Apelido" value="{{ $user->pessoa->apelidoFantasia != null ? $user->pessoa->apelidoFantasia : old('apelidoFantasia') }}">
                                        @error('apelidoFantasia')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">*CPF</label>
                                        <input class="cpf form-control @error('cpf') is-invalid @enderror" type="text" name="cpf" id="cpf" placeholder="Informe seu CPF" value="{{ $user->cpf != null ? $user->cpf: old('cpf') }}">
                                        @error('cpf')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">*Data de Nascimento</label>
                                        <input class="dataFormat form-control @error('dt_nascimento_fundacao') is-invalid @enderror" type="date" min="1899-01-01" max="2000-13-13" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ $user->pessoa->dt_nascimento_fundacao != null ? $user->pessoa->dt_nascimento_fundacao : old('dt_nascimento_fundacao') }}">
                                        @error('dt_nascimento_fundacao')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">*Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Informe um email válido" value="{{ $user->email != null ? $user->email : old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Celular/Telefone</label>
                                        <input class="telefone form-control @error('telefone_celular') is-invalid @enderror" type="text"  name="telefone_celular" value="{{ $user->telefone_celular != null ? $user->telefone_celular : old('telefone_celular') }}">
                                        @error('telefone_celular')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Celular/Telefone Recado</label>
                                        <input class="telefone form-control @error('telefone_celular2') is-invalid @enderror" type="text" name="telefone_celular2" value="{{ $user->telefone_celular2 != null ? $user->telefone_celular2 : old('telefone_celular2') }}">
                                        @error('telefone_celular2')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Endereço</h5>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="cep">CEP</label>
                                        <input type="text" name="cep" id="cep" class="form-control @error('cep') is-invalid @enderror" placeholder="Informe o CEP" value="{{ $user->pessoa->cep != null ? $user->pessoa->cep : old('cep') }}">
                                        @error('cep')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="endereco">Endereço (Rua/Avenida)</label>
                                        <input type="text" name="endereco" id="endereco" class="form-control @error('endereco') is-invalid @enderror" placeholder="Informe o endereço" value="{{ $user->pessoa->endereco != null ? $user->pessoa->endereco : old('endereco') }}">
                                        @error('endereco')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="numero">Número</label>
                                        <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" placeholder="Informe o número" value="{{ $user->pessoa->numero != null ? $user->pessoa->numero : old('numero') }}">
                                        @error('numero')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="bairro">Bairro / Comunidade</label>
                                        <input type="text" name="bairro" id="bairro" class="form-control @error('bairro') is-invalid @enderror" placeholder="Informe o bairro" value="{{ $user->pessoa->bairro != null ? $user->pessoa->bairro : old('bairro') }}">
                                        @error('bairro')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="complemento">Complemento</label>
                                        <input type="text" name="complemento" id="complemento" class="form-control @error('complemento') is-invalid @enderror" placeholder="Informe o complemento" value="{{ $user->pessoa->complemento != null ? $user->pessoa->complemento : old('complemento') }}">
                                        @error('complemento')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="ponto_referencia">Ponto de Referência</label>
                                        <input type="text" name="ponto_referencia" class="form-control @error('ponto_referencia') is-invalid @enderror" placeholder="Informe o ponto de referência" value="{{ $user->pessoa->ponto_referencia != null ? $user->pessoa->ponto_referencia : old('ponto_referencia') }}">
                                        @error('ponto_referencia')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Troca de senha</h5>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="form-label">Senha Antiga</label>
                                        <input class="form-control" type="password" name="senha_antiga" placeholder="Informe a senha antiga" value="{{ old('senha_antiga') }}">
                                        {{-- @error('senha_antiga')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror --}}
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">Senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                        <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Informe uma senha" value="{{ old('password') }}">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">Confirme a senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                        <input class="form-control @error('confirmacao') is-invalid @enderror" type="password" name="confirmacao" placeholder="Confirme a senha" value="{{ old('confirmacao') }}">
                                        @error('confirmacao')
                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8-beta.17/inputmask.js" integrity="sha512-XvlcvEjR+D9tC5f13RZvNMvRrbKLyie+LRLlYz1TvTUwR1ff19aIQ0+JwK4E6DCbXm715DQiGbpNSkAAPGpd5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script>
    $('.cpf').mask('000.000.000-00');
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
            id_municipio:{
                required:true
            },
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
            id_municipio:{
                required:"Campo obrigatório"
            },
        }
    });

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

                    //Vamos incluir para que o Número seja focado automaticamente
                    //melhorando a experiência do usuário
                    if (resposta.logradouro != null){
                        $("#numero").focus();
                    }
                    else{
                        $("#endereco").focus();
                    }

                    alterarMunicipio(resposta.ibge);
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

    });
</script>

@endsection

