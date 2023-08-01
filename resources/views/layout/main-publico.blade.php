<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>IDR-Paraná</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="shortcut icon" type="svg" href="{{ asset('image/layer-group-solid.svg') }}" style="color: #4a88eb">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.css" />
    <link href="{{ asset('select2-4.1.0/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('select2-bootstrap/dist/select2-bootstrap.css') }}" />
    <script src="{{ asset('js/jquery.js') }}"></script>
</head>

@php
    use App\Models\Encaminhamento;
    // use App\AvisoVisualizacao;
    use App\Models\MensagemVisualizacao;
    if (Auth::user()) {
        $visu = Encaminhamento::where('id_destinatario', '=', Auth::user()->id)
            ->where('visto', '=', 0)
            ->get();
        $countVisu = count($visu);

        $visuMsg = MensagemVisualizacao::where('id_user', '=', Auth::user()->id)
            ->where('visto', '=', 0)
            ->get();
        $countVisuMsg = count($visuMsg);
    }
@endphp


<div class="wrapper">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-content js-simplebar">
            <a class="sidebar-brand" href="">
                <div class="max-width">
                    <div class="imageContainer">
                        <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.jpg'))) }}" class="img-thumbnail" width="30%" height="30%" alt="">
                        <span class="align-middle mr-3" style="font-size: .999rem;">IDR-Paraná</span>
                    </div>
                </div>
                {{-- <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.jpg'))) }}"
                alt="" width="40px" height="40px" />
                <span class="align-middle mr-3" style="font-size: .999rem;">IDR-Paraná</span> --}}
            </a>

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Páginas
                </li>

                @if (Auth::guest())
                    <li class="sidebar-item ">
                        <a href="#" class="sidebar-link">

                            <span> Fazer Login para obter acesso ao sistema e realizar download do acervo.</span>
                        </a>
                    </li>
                    <li style=" text-align:center; justify:center ">
                        <a class="btn btn-primary" style="color: white" href="{{ route('login', $links='link?'.$link) }}"
                            id="messagesDropdown" data-bs-toggle="dropdown">
                            <span>Login</span>
                        </a>
                    </li>
                @else
                    @if (Auth::check())
                        <a href="{{ route('home') }}" class="sidebar-link">
                            <span>Tela Inicial</span>
                        </a>
                    @endif
                @endif


            </ul>
        </div>
    </nav>





    <div class="main">
        <nav class="navbar navbar-expand navbar-light navbar-bg">


            @if (Auth::guest())
                <a class="sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>
            @endif
            @if (Auth::check())
                <a class="sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>
            @endif

            <div class="navbar-collapse collapse">
                {{-- <ul class="navbar-nav">
                    <form action="{{ route('home.alterarPerfil') }}" id="form-alterar-perfil" method="POST">
                        @csrf
                        @method('POST')
                        <li class="nav-item">
                            <select name="perfil_ativo" id="perfil_ativo">
                                @foreach (Auth::user()->tipo_perfis_ativos as $up)
                                    <option value="{{ $up->id_perfil }}" {{ Auth::user()->id_tipo_perfil == $up->id_tipo_perfil ? 'selected' : ''}}>{{ $up->tipo_perfil->descricao }}</option>
                                @endforeach
                            </select>
                        </li>
                    </form>
                </ul> --}}

                <ul class="navbar-nav navbar-align">

                    <a href="#">
                        <span class="glyphicon glyphicon-log-out"></span>
                    </a>
                    @if (Auth::guest())
                        {{-- <li>
                            <a class="btn btn-primary" style="color: white" href="{{ route('login') }}"
                                id="messagesDropdown" data-bs-toggle="dropdown">
                                <span>Login</span>
                            </a>
                        </li> --}}

                    @endif
                </ul>
            </div>
        </nav>

        <main class="content">
            @yield('content2')
        </main>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-6 text-left">
                    </div>
                    <div class="col-6 text-right">
                        <p class="mb-0">
                            {{-- &copy; 2022 - <a href="" class="text-muted">IDR - Paraná</a> --}}
                            © <?php echo date('Y'); ?> - <a href="" class="text-muted">IDR - Paraná</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"
    integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('jquery-mask/dist/jquery.mask.min.js') }}"></script>
<script src="{{ url('js/fontawesome.js') }}"></script>
<script src="{{ url('js/bootstrap.js') }}"></script>
<script src="{{ url('js/functions.js') }}"></script>
<script src="{{ url('js/prevent_multiple_submits.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.js">
</script>
<script src="{{ asset('select2-4.1.0/dist/js/select2.min.js') }}"></script>

<script>
    $('#perfil_ativo').on('change', function() {
        $('#form-alterar-perfil').submit();
    });
</script>

@yield('scripts')

</html>