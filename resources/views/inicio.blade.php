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
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.css" />

    <style>
        .error {
            color: red
        }
    </style>
</head>

<body>
    <div class="main d-flex justify-content-center w-100">
        <nav class="navbar navbar-expand-md shadow-sm" style="background-color: #00008b">
            <div class="container">
                <a class="sidebar-brand" href="{{ url('/') }}">
                    <div class="max-width">
                        <div class="imageContainer">
                            <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.png'))) }}"
                                class="img-thumbnail" width="80px" height="60px" alt="">
                            <span class="align-middle mr-3" style="font-size: .999rem;">SisCamara</span>
                        </div>
                    </div>
                    {{-- <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.jpg'))) }}"
                    class="align-middle mr-3" alt="" width="80px" height="60px" />
                    <span class="align-middle mr-3" style="font-size: .999rem;">Instituto de Desenvolvimento Rural - Paraná</span> --}}
                </a>
                {{-- <div class="container">
                <a class="sidebar-brand" href="{{ url('/') }}">
                    <span class="align-middle mr-3" style="font-size: .999rem;">Instituto de Desenvolvimento Rural -
                        Paraná</span>
                </a>
            </div> --}}
                {{-- <ul class="nav justify-content-end">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <button type="button" class="btn btn-outline-light btn-lg">Web Pública</button>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <button type="button" class="btn btn-outline-success btn-lg">Votação</button>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <button type="button" class="btn btn-outline-warning btn-lg">Atos</button>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <button type="button" class="btn btn-outline-danger btn-lg">E-SIC</button>
                    </a>
                </li>
            </ul> --}}
        </nav>
        <main class="content d-flex p-0">
            <div class="container d-flex flex-column">
                <div class="row h-100">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">
                            <div class="text-center mt-4">
                                <h1 class="h2">
                                    Página Inicial
                                </h1>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="m-sm-4">
                                        <div class="mt-3">
                                            <a href="{{ route('login') }}">
                                                <button type="button" class="btn btn-lg btn-dark"
                                                    style="width: 100%; margin-bottom: 0.7rem">Login</button>
                                            </a>
                                        </div>
                                        <div class="mt-3">
                                            <a href="#">
                                                <button type="button" class="btn btn-lg btn-primary"
                                                    style="width: 100%; margin-bottom: 0.7rem">Web Pública</button>
                                            </a>
                                        </div>
                                        <div class="mt-3">
                                            <a href="#">
                                                <button type="button" class="btn btn-lg btn-success"
                                                    style="width: 100%; margin-bottom: 0.7rem">Votação</button>
                                            </a>
                                        </div>
                                        <div class="mt-3">
                                            <a href="#">
                                                <button type="button" class="btn btn-lg btn-warning"
                                                    style="width: 100%; margin-bottom: 0.7rem">Atos</button>
                                            </a>
                                        </div>
                                        <div class="mt-3">
                                            <a href="#">
                                                <button type="button" class="btn btn-lg btn-danger"
                                                    style="width: 100%; margin-bottom: 0.7rem">E-SIC</button>
                                            </a>
                                        </div>
                                    </div>
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
                        <p class="mb-0">
                            © <?php echo date('Y'); ?> - <a href="http://agile.inf.br" target="_blank"
                                class="text-muted">Agile Tecnologia</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"
        integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ url('js/bootstrap.js') }}"></script>
    <script src="{{ asset('jquery-mask/src/jquery.mask.js') }}"></script>
    <script>
        $('#cpf').mask('000.000.000-00');
    </script>

</body>

</html>
