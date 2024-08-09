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
    @include('sweetalert::alert')

    <h1 class="h3 mb-3">Alteração de Agente Político</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('agente_politico.update', $agente_politico->id_user) }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Pleito Eleitoral</label>
                            <select name="id_pleito_eleitoral" id="id_pleito_eleitoral" class="select2 form-control @error('id_pleito_eleitoral') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($pleito_eleitorals as $pleito_eleitoral)
                                    <option value="{{ $pleito_eleitoral->id }}" {{ $agente_politico->id_pleito_eleitoral == $pleito_eleitoral->id ? 'selected' : ''}}>
                                        {{ $pleito_eleitoral->ano_pleito }} -
                                        Mandato <strong>{{ date('d/m/Y', strtotime($agente_politico->dataInicioMandato)) }}</strong> - <strong>{{ date('d/m/Y', strtotime($agente_politico->dataFimMandato))}}</strong>
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pleito_eleitoral')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Cargo Eletivo</label>
                            <select name="id_cargo_eletivo" id="id_cargo_eletivo" class="select2 form-control @error('id_cargo_eletivo') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @for ($i=0; $i<Count($cargos_eletivos); $i++)
                                    <option value="{{ $cargos_eletivos[$i]['id'] }}" {{ $agente_politico->id_cargo_eletivo == $cargos_eletivos[$i]['id'] ? 'selected' : ''}}>
                                        {{ $cargos_eletivos[$i]['descricao'] }}
                                    </option>
                                @endfor
                            </select>
                            @error('id_cargo_eletivo')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Data início mandato</label>
                            <input type="date" name="dataInicioMandato" class="form-control @error('dataInicioMandato') is-invalid @enderror" value="{{ $agente_politico->dataInicioMandato }}">
                            @error('dataInicioMandato')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Data fim mandato</label>
                            <input type="date" name="dataFimMandato" class="form-control @error('dataFimMandato') is-invalid @enderror" value="{{ $agente_politico->dataFimMandato }}">
                            @error('dataFimMandato')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
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
                                    <br>
                                    <div class="cpf text-muted mb-2">{{ $agente_politico->usuario->cpf }}</div>
                                    <h4 class="mb-2 underline"><strong>{{ $agente_politico->usuario->pessoa->nome }}</strong></h4>
                                    <h4 class="mb-0">{{ $agente_politico->usuario->email }}</h4>
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
                                                        <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe seu nome" value="{{ $agente_politico->usuario->pessoa->nome != null ? $agente_politico->usuario->pessoa->nome : old('nome') }}">
                                                        @error('nome')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label">Apelido</label>
                                                        <input class="form-control @error('apelidoFantasia') is-invalid @enderror" type="text" name="apelidoFantasia" id="apelidoFantasia" placeholder="Apelido" value="{{ $agente_politico->usuario->pessoa->apelidoFantasia != null ? $agente_politico->usuario->pessoa->apelidoFantasia : old('apelidoFantasia') }}">
                                                        @error('apelidoFantasia')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label">*CPF</label>
                                                        <input class="cpf form-control @error('cpf') is-invalid @enderror" type="text" name="cpf" id="cpf" placeholder="Informe seu CPF" value="{{ $agente_politico->usuario->cpf != null ? $agente_politico->usuario->cpf: old('cpf') }}">
                                                        @error('cpf')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label">*Data de Nascimento</label>
                                                        <input class="dataFormat form-control @error('dt_nascimento_fundacao') is-invalid @enderror" type="date" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ $agente_politico->usuario->pessoa->dt_nascimento_fundacao != null ? $agente_politico->usuario->pessoa->dt_nascimento_fundacao : old('dt_nascimento_fundacao') }}">
                                                        @error('dt_nascimento_fundacao')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label">*Email</label>
                                                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Informe um email válido" value="{{ $agente_politico->usuario->email != null ? $agente_politico->usuario->email : old('email') }}">
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label">Celular/Telefone</label>
                                                        <input class="telefone form-control @error('telefone_celular') is-invalid @enderror" type="text"  name="telefone_celular" value="{{ $agente_politico->usuario->telefone_celular != null ? $agente_politico->usuario->telefone_celular : old('telefone_celular') }}">
                                                        @error('telefone_celular')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label">Celular/Telefone Recado</label>
                                                        <input class="telefone form-control @error('telefone_celular2') is-invalid @enderror" type="text" name="telefone_celular2" value="{{ $agente_politico->usuario->telefone_celular2 != null ? $agente_politico->usuario->telefone_celular2 : old('telefone_celular2') }}">
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
                                                        <input type="text" name="cep" id="cep" class="form-control @error('cep') is-invalid @enderror" placeholder="Informe o CEP" value="{{ $agente_politico->usuario->pessoa->cep != null ? $agente_politico->usuario->pessoa->cep : old('cep') }}">
                                                        @error('cep')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="endereco">Endereço (Rua/Avenida)</label>
                                                        <input type="text" name="endereco" id="endereco" class="form-control @error('endereco') is-invalid @enderror" placeholder="Informe o endereço" value="{{ $agente_politico->usuario->pessoa->endereco != null ? $agente_politico->usuario->pessoa->endereco : old('endereco') }}">
                                                        @error('endereco')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="numero">Número</label>
                                                        <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" placeholder="Informe o número" value="{{ $agente_politico->usuario->pessoa->numero != null ? $agente_politico->usuario->pessoa->numero : old('numero') }}">
                                                        @error('numero')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="bairro">Bairro / Comunidade</label>
                                                        <input type="text" name="bairro" id="bairro" class="form-control @error('bairro') is-invalid @enderror" placeholder="Informe o bairro" value="{{ $agente_politico->usuario->pessoa->bairro != null ? $agente_politico->usuario->pessoa->bairro : old('bairro') }}">
                                                        @error('bairro')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="complemento">Complemento</label>
                                                        <input type="text" name="complemento" id="complemento" class="form-control @error('complemento') is-invalid @enderror" placeholder="Informe o complemento" value="{{ $agente_politico->usuario->pessoa->complemento != null ? $agente_politico->usuario->pessoa->complemento : old('complemento') }}">
                                                        @error('complemento')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="ponto_referencia">Ponto de Referência</label>
                                                        <input type="text" name="ponto_referencia" class="form-control @error('ponto_referencia') is-invalid @enderror" placeholder="Informe o ponto de referência" value="{{ $agente_politico->usuario->pessoa->ponto_referencia != null ? $agente_politico->usuario->pessoa->ponto_referencia : old('ponto_referencia') }}">
                                                        @error('ponto_referencia')
                                                            <div class="invalid-feedback">{{ $message }}</div><br>
                                                        @enderror
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
                    <br>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
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
