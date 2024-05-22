@extends('layout.main')

@section('content')

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
                            <h4 style="text-align: center">
                                Departamento(s) vinculado(s): &nbsp
                            </h4>
                            <h5>
                                @if (count($departamentos) != null)
                                    @foreach ($departamentos as $departamento)
                                        <li>
                                            {{ $departamento->departamento->descricao }}
                                        </li>
                                    @endforeach
                                @else
                                    Não está relacionado a nenhum departamento
                                @endif
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
                        @if ($user->pessoa->pessoaJuridica == 0)
                            @include('home.dados-pf.dadosPf')
                        @else
                            @include('home.dados-pj.dadosPj')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
    <script>
        $('.cpf').mask('000.000.000-00');
        $('.cnpj').mask('00.000.000/0000-00');
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

