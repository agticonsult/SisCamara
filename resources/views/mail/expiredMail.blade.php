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
        <nav class="navbar navbar-expand-md shadow-sm" style="background-color: #4a88eb">
            <div class="container">
                <a class="sidebar-brand" href="{{ url('/') }}">
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
                                <h1 class="h2">Confirmação de e-mail com sucesso</h1>
                                <p>Aguarde 10 segundos para ser redirecionado para página de Login</p>

                                <h2 id="timer"></h2>

                                @if (session())
                                    <script>
                                        setTimeout(function() {
                                            window.location = "/login" //retorna para página de login
                                        }, 10000); // 10000 = 10 s
                                    </script>
                                @endif

                                <div class="mt-2">
                                    <a type="submit" href="{{ route('login') }}" class="btn btn-lg btn-primary" style="width: 80%">Redirecionar</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-12 text-right">
                        {{-- <p class="mb-0">
                            &copy; 2022 - <a href="http://agile.inf.br" class="text-muted">Agile Tecnologia</a>
                        </p> --}}
                        <p class="mb-0">
                            © <?php echo date('Y'); ?> - <a href="http://agile.inf.br" target="_blank" class="text-muted">Agile Tecnologia</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ url('js/bootstrap.js') }}"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
{{-- função relógio para imprimir na tela do usuário --}}
<script src="{{asset('js/timer.js')}}"></script>

</body>
</html>




