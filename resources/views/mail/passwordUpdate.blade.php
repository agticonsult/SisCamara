<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SisCamara</title>
    <link rel="shortcut icon" type="svg" href="{{ asset('image/layer-group-solid.svg') }}" style="color: #4a88eb">

    {{-- Styles --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.css"/>
    <style>
        .error{
              color:red
        }
    </style>
</head>
<body>
    <div class="main d-flex justify-content-center w-100">
        <nav class="navbar navbar-expand-md shadow-sm" style="background-color: #1e70b8">
            <div class="container">
                <a class="sidebar-brand" href="{{ url('/') }}">
                    <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.png'))) }}" class="img-thumbnail" width="80px" height="60px" alt="">
                    <span class="align-middle mr-3" style="font-size: .999rem;">SisCamara</span>
                </a>
            </div>
        </nav>
        <main class="content d-flex p-0">
            <div class="container d-flex flex-column">
                <div class="row h-100">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">
                            <div class="text-center mt-4">
                                <h1 class="h2">
                                    Alteração de senha
                                </h1>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="m-sm-4">
                                        <form action="{{route('passwordReset4', Crypt::encrypt($user->id))}}" method="POST" id="form">
                                            @csrf
                                            @method('POST')
                                            @include('errors.alerts')
                                            @include('errors.errors')
                                            {{-- <div class="mb-3">
                                                <label for="cpf">CPF</label>
                                                <input type="text" name="cpf" id="cpf" class="form-control form-control-lg" placeholder="Digite seu CPF">
                                            </div> --}}
                                            <div class="mb-3">
                                                <label for="password">Informe a nova senha (minímo 6 e máximo 35 caracteres)</label>
                                                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Informe a nova senha" >
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirmacao">Confirme a senha</label>
                                                <input type="password" name="confirmacao" id="confirmacao" class="form-control form-control-lg" placeholder="Confirme a senha" >
                                            </div>
                                            <div class="text-center mt-3">
                                                <button type="submit" class="btn btn-lg btn-primary" style="width: 100%">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ url('js/bootstrap.js') }}"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
{{-- <script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script> --}}
<script>
    // $('#cpf').mask('000.000.000-00');

    $("#form").validate({
        rules : {
            cpf:{
                required:true,
                minlength:14,
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
        },
        messages:{
            cpf:{
                required:"Campo obrigatório",
                minlength:"Minímo 11 caracteres"
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
        }
    });
</script>


</body>
</html>




