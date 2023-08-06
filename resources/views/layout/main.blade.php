<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>SisCamara</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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


{{-- @php
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
            ->where('ativo', '=', 1)
            ->get();
        $countVisuMsg = count($visuMsg);
    }
@endphp --}}


<div class="wrapper">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar sidebar-content js-simplebar">
            <a class="sidebar-brand" href="">
                <div class="max-width">
                    <div class="imageContainer">
                        <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.png'))) }}"
                            class="img-thumbnail" width="30%" height="30%" alt="">
                        <span class="align-middle mr-3" style="font-size: .999rem;">SisCamara</span>
                        {{-- <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.jpg'))) }}"
                            class="img-thumbnail" width="30%" height="30%" alt="">
                        <span class="align-middle mr-3" style="font-size: .999rem;">IDR-Paraná</span> --}}
                    </div>
                </div>
            </a>

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Páginas
                </li>

                @if (Auth::user())
                    <li class="sidebar-item {{ Route::current()->uri == 'home' ? 'active' : null }}">
                        <a href="{{ route('home') }}" class="sidebar-link">
                            <i class="fas fa-user-circle"></i>
                            Dados do Usuário
                        </a>
                    </li>
                @endif

                @if (Auth::user()->temPermissao('Ato', 'Listagem') == 1)
                    <li class="sidebar-item {{ Route::current()->getPrefix() == 'ato' ? 'active' : null }}">
                        <a href="{{ route('ato.index') }}" class="sidebar-link">
                            <i class="fa fa-list-alt" aria-hidden="true"></i>
                            <span>Atos</span>
                        </a>
                    </li>
                @endif

                {{-- Configuração --}}
                @if (Auth::user()->temPermissao('FinalidadeGrupo', 'Listagem') == 1 ||
                        Auth::user()->temPermissao('FinalidadeGrupo', 'Cadastro') == 1 ||
                        Auth::user()->temPermissao('FinalidadeGrupo', 'Alteração') == 1 ||
                        Auth::user()->temPermissao('FinalidadeGrupo', 'Relatório') == 1 ||
                        Auth::user()->temPermissao('Filesize', 'Listagem') == 1 ||
                        Auth::user()->temPermissao('Filesize', 'Cadastro') == 1 ||
                        Auth::user()->temPermissao('Filesize', 'Alteração') == 1 ||
                        Auth::user()->temPermissao('Filesize', 'Relatório') == 1 ||
                        Auth::user()->temPermissao('TipoEvento', 'Listagem') == 1 ||
                        Auth::user()->temPermissao('TipoEvento', 'Cadastro') == 1 ||
                        Auth::user()->temPermissao('TipoEvento', 'Alteração') == 1 ||
                        Auth::user()->temPermissao('TipoEvento', 'Relatório') == 1)
                    <li class="sidebar-item">
                        <a href="#configuracao" data-toggle="collapse" class="sidebar-link collapsed">
                            <i class="fas fa-cog"></i>
                            Configuração
                        </a>
                        <ul id="configuracao"
                            class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == 'configuracao/finalidade-grupo' ||
                            Route::current()->getPrefix() == 'configuracao/tamanho-anexo' ||
                            Route::current()->getPrefix() == 'configuracao/tipo-evento'
                                ? 'active'
                                : 'collapse' }}">
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/finalidade-grupo' ? 'active' : null }}">
                                <a class="sidebar-link "
                                    href="{{ route('configuracao.finalidade_grupo.index') }}">Finalidade dos
                                    Grupos de Usuário</a>
                            </li>
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/tamanho-anexo' // mudar as rotas e criar a pagina
                                    ? 'active'
                                    : null }}">
                                <a class="sidebar-link " href="{{ route('configuracao.tamanho_anexo.index') }}">Tamanho
                                    dos Anexos</a>
                            </li>
                            {{-- <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/tipo-evento' // mudar as rotas e criar a pagina
                                    ? 'active'
                                    : null }}">
                                <a class="sidebar-link " href="{{ route('configuracao.tipo_evento.index') }}">Tipos de
                                    Evento</a>
                            </li> --}}
                        </ul>
                    </li>
                @endif

                {{-- Usuários --}}
                @if (Auth::user()->temPermissao('User', 'Listagem') == 1)
                    <li class="sidebar-item">
                        <a href="#usuarios" data-toggle="collapse" class="sidebar-link collapsed">
                            <i class="fas fa-users"></i>
                            Usuários
                        </a>
                        <ul id="usuarios"
                            class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/usuario' || Route::current()->getPrefix() == '/auditoria'
                                ? 'active'
                                : 'collapse' }}">
                            <li
                                class="sidebar-item {{ Route::current()->uri == 'usuario/index' || Route::current()->uri == 'usuario/edit/{id}' ? 'active' : null }}">
                                <a class="sidebar-link" href="{{ route('usuario.index') }}">Listagem</a>
                            </li>
                            <li class="sidebar-item {{ Route::current()->uri == 'usuario/create' ? 'active' : null }}">
                                <a class="sidebar-link" href="{{ route('usuario.create') }}">Cadastro</a>
                            </li>
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == '/auditoria' ? 'active' : null }}">
                                <a class="sidebar-link" href="{{ route('auditoria.index') }}">Auditoria</a>
                            </li>
                        </ul>
                    </li>
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
                                    <option value="{{ $up->id_tipo_perfil }}" {{ Auth::user()->id_tipo_perfil == $up->id_tipo_perfil ? 'selected' : ''}}>{{ $up->tipo_perfil->descricao }}</option>
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
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
                                data-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                                <span class="text-dark"></span>
                            </a>
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
                                data-toggle="dropdown">
                                <span class="avatar"> {{ auth()->user()->pessoa->nomeCompleto }}</span>
                                <span class="text-dark"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Sair
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>

        <main class="content">
            @yield('content')
        </main>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-6 text-left">
                    </div>
                    <div class="col-6 text-right">
                        <p class="mb-0">
                            {{-- &copy; 2022 - <a href="" class="text-muted">IDR - Paraná</a> --}}
                            © <?php echo date('Y'); ?> - <a href="" class="text-muted">SisCamara</a>
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
