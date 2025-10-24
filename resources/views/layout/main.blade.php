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
        {{-- <link rel="stylesheet" href="{{ asset('select2-bootstrap/dist/select2-bootstrap.css') }}" /> --}}
        <script src="{{ asset('js/jquery.js') }}"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css" integrity="sha512-Fm8kRNVGCBZn0sPmwJbVXlqfJmPC13zRsMElZenX6v721g/H7OukJd8XzDEBRQ2FSATK8xNF9UYvzsCtUpfeJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/table/ui/trumbowyg.table.min.css" integrity="sha512-qIa+aUEbRGus5acWBO86jFYxOf4l/mfgb30hNmq+bS6rAqQhTRL5NSOmANU/z5RXc3NJ0aCBknZi6YqD0dqoNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" /> --}}

    </head>

    <style>
        .sidebar {
            background-color: #0f4e96 !important;
        }
        .sidebar-item.active > a {
            background-color: #386ca0 !important;
            /* color: rgb(201, 187, 187) */
        }
        .sidebar-link{
            color: rgb(211, 199, 199) !important;
        }
        .sidebar-content{
            background-color: rgba(0, 0, 0, 0) !important;
        }
        .error{
            color:red
        }
        .caminho {
            font-size: 12px;
        }
    </style>

    <body>
        <div class="wrapper">
            <nav id="sidebar" class="sidebar">
                <div class="sidebar sidebar-content js-simplebar">
                    <a class="sidebar-brand" href="">
                        <div class="max-width">
                            <div class="imageContainer">
                                <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.png'))) }}"
                                    class="img-thumbnail" width="30%" height="30%" alt="">
                                <span class="align-middle mr-3" style="font-size: .999rem;">SisCamara</span>
                            </div>
                        </div>
                    </a>

                    <ul class="sidebar-nav">
                        <li class="sidebar-header">
                            Páginas
                        </li>

                        {{-- Home --}}
                        @if (Auth::user())
                            <li class="sidebar-item {{ Route::current()->uri == 'home' ? 'active' : null }}">
                                <a href="{{ route('home') }}" class="sidebar-link">
                                    <i class="fas fa-fw fa-user-edit"></i>
                                    Dados do Usuário
                                </a>
                            </li>
                        @endif

                        {{-- Atos --}}
                        @if (Auth::user()->temPermissao('Ato', 'Listagem') == 1)
                            <li class="sidebar-item {{ Route::current()->uri == 'ato' || Route::current()->uri == 'ato/create' || Route::current()->uri == 'ato/show/{id}' || Route::current()->uri == 'ato/dados-gerais/edit/{id}' || Route::current()->uri == 'ato/corpo-do-texto/edit/{id}' || Route::current()->uri == 'ato/anexos/edit/{id}' ? 'active' : null }}">
                                <a href="{{ route('ato.index') }}" class="sidebar-link">
                                    <i class="fas fa-fw fa-file-alt" aria-hidden="true"></i>
                                    <span>Atos</span>
                                </a>
                            </li>
                        @endif

                        {{-- Votação Eletrônica --}}
                        @if (Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') == 1)
                            <li class="sidebar-item">
                                <a href="#votacaoEletronica" data-toggle="collapse" class="sidebar-link collapsed">
                                    <i class="fas fa-chess-king"></i>Votação Eletrônica
                                </a>
                                <ul id="votacaoEletronica"
                                    class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/votacao-eletronica' ||
                                    Route::current()->getPrefix() == 'votacao-eletronica/vereador'
                                        ? 'active'
                                        : 'collapse' }}">
                                    @if (Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') == 1)
                                        <li
                                            class="sidebar-item {{ Route::current()->getPrefix() == '/votacao-eletronica' ? 'active' : null }}">
                                            <a href="{{ route('votacao_eletronica.index') }}" class="sidebar-link">
                                                Gerenciar Votações
                                            </a>
                                        </li>
                                    @endif

                                    @if (Auth::user()->ehAgentePolitico() == 1)
                                        <li
                                            class="sidebar-item {{ Route::current()->getPrefix() == 'votacao-eletronica/vereador' ? 'active' : null }}">
                                            <a href="{{ route('votacao_eletronica.vereador.index') }}" class="sidebar-link">
                                                Acompanhar Votações
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </li>
                        @endif

                        {{-- Modelo de Proposição --}}
                        @if (Auth::user()->temPermissao('ModeloProposicao', 'Listagem') == 1 ||
                                Auth::user()->temPermissao('Proposicao', 'Listagem') == 1 ||
                                Auth::user()->temPermissao('Legislatura', 'Listagem') == 1 ||
                                Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') == 1
                            )
                            <li class="sidebar-item">
                                <a href="#processoLegislativo" data-toggle="collapse" class="sidebar-link collapsed">
                                    <i class="fas fa-fw fa-clipboard"></i>Processo Legislativo
                                </a>
                                <ul id="processoLegislativo"
                                    class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/proposicao' ||
                                    Route::current()->getPrefix() == 'proposicao/modelo' ||
                                    Route::current()->getPrefix() == 'processo-legislativo/legislatura' ||
                                    Route::current()->getPrefix() == 'processo-legislativo/pleito-eleitoral'
                                        ? 'active'
                                        : 'collapse' }}">
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'proposicao/modelo' ? 'active' : null }}">
                                        <a href="{{ route('proposicao.modelo.index') }}" class="sidebar-link">
                                            Modelos de Proposição
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == '/proposicao' ? 'active' : null }}">
                                        <a href="{{ route('proposicao.index') }}" class="sidebar-link">
                                            Proposição
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'processo-legislativo/legislatura' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('processo_legislativo.legislatura.index') }}">Legislaturas</a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'processo-legislativo/pleito-eleitoral' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('processo_legislativo.pleito_eleitoral.index') }}">Pleitos
                                            Eleitorais</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- Agente Político --}}
                        @if (Auth::user()->temPermissao('AgentePolitico', 'Listagem') == 1)
                            <li
                                class="sidebar-item {{ Route::current()->uri == 'agente-politico' || Route::current()->uri == 'agente-politico/create' || Route::current()->uri == 'agente-politico/novo-agente-politico' || Route::current()->uri == 'agente-politico/vincular' || Route::current()->uri == 'agente-politico/edit/{id}'? 'active' : null }}">
                                <a href="{{ route('agente_politico.index') }}" class="sidebar-link">
                                    <i class="fas fa-fw fa-user-tie" aria-hidden="true"></i>
                                    <span>Agentes Políticos</span>
                                </a>
                            </li>
                        @endif

                        {{-- Repartição --}}
                        {{-- @if (Auth::user()->temPermissao('Reparticao', 'Listagem') == 1)
                            <li class="sidebar-item {{ Route::current()->uri == 'reparticao/index' || Route::current()->uri == 'reparticao/create' || Route::current()->uri == 'reparticao/edit/{id}' ? 'active' : null }}">
                                <a href="{{ route('reparticao.index') }}" class="sidebar-link">
                                    <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    <span>Repartição</span>
                                </a>
                            </li>
                        @endif --}}

                        {{-- Documentos --}}
                        @if (Auth::user()->temPermissao('Documento', 'Listagem') == 1)
                            <li class="sidebar-item {{ Route::current()->uri == 'documento' || Route::current()->uri == 'documento/create' || Route::current()->uri == 'documento/edit/{id}' || Route::current()->uri == 'documento/show/{id}' ? 'active' : null }}">
                                <a href="{{ route('documento.index') }}" class="sidebar-link">
                                    <i class="fa fa-book" aria-hidden="true"></i>
                                    <span>Documentos</span>
                                </a>
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
                                    <li
                                        class="sidebar-item {{ Route::current()->uri == 'usuario/create' ? 'active' : null }}">
                                        <a class="sidebar-link" href="{{ route('usuario.create') }}">Cadastro</a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == '/auditoria' ? 'active' : null }}">
                                        <a class="sidebar-link" href="{{ route('auditoria.index') }}">Auditoria</a>
                                    </li>
                                    {{-- @if(Auth::user()->permissaoAprovacaoUsuario())
                                        <li class="sidebar-item {{ Route::current()->uri == 'usuario/aprovacao-cadastros-externos' ? 'active' : null }}">
                                            <a href="{{ route('usuario.aprovacaoCadastroUsuario') }}" class="sidebar-link">
                                                Aprovação de cadastro de Usuários Externos
                                            </a>
                                        </li>
                                    @endif --}}
                                </ul>
                            </li>
                        @endif

                        @if(Auth::user()->permissaoCadastrarUsuario())
                            <li class="sidebar-item">
                                <a href="#usuariosExternos" data-toggle="collapse" class="sidebar-link collapsed">
                                    <i class="fas fa-users"></i>
                                    Usuários
                                </a>
                                <ul id="usuariosExternos"
                                    class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/usuario-externo'
                                        ? 'active'
                                        : 'collapse' }}">
                                    {{-- <li
                                        class="sidebar-item {{ Route::current()->uri == 'usuario/index' || Route::current()->uri == 'usuario/edit/{id}' ? 'active' : null }}">
                                        <a class="sidebar-link" href="{{ route('usuario.index') }}">Listagem</a>
                                    </li> --}}
                                    <li
                                        class="sidebar-item {{ Route::current()->uri == 'usuario-externo/create' ? 'active' : null }}">
                                        <a class="sidebar-link" href="{{ route('usuario_externo.create') }}">Cadastro</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- Aprovação de cadastros externos --}}
                        @if(Auth::user()->permissaoAprovacaoUsuario())
                            <li class="sidebar-item {{ Route::current()->uri == 'aprovacao-cadastro-usuario/usuarios' ? 'active' : null }}">
                                <a href="{{ route('aprovacao_cadastro_usuario.aprovacaoCadastroUsuario') }}" class="sidebar-link">
                                    <i class="fas fa-fw fa-user"></i>
                                    Aprovação de cadastro de Usuários Externos
                                </a>
                            </li>
                        @endif

                        {{-- Configuração --}}
                        @if (Auth::user()->temPermissao('FinalidadeGrupo', 'Listagem') == 1 ||
                                Auth::user()->temPermissao('Filesize', 'Listagem') == 1 ||
                                Auth::user()->temPermissao('TipoEvento', 'Listagem') == 1 ||
                                Auth::user()->temPermissao('PleitoEleitoral', 'Listagem') == 1)
                            <li class="sidebar-item">
                                <a href="#configuracao" data-toggle="collapse" class="sidebar-link collapsed">
                                    <i class="fas fa-cog"></i>
                                    Configuração
                                </a>
                                <ul id="configuracao"
                                    class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == 'configuracao/finalidade-grupo' ||
                                    Route::current()->getPrefix() == 'configuracao/assunto-ato' ||
                                    Route::current()->getPrefix() == 'configuracao/classificacao-ato' ||
                                    Route::current()->getPrefix() == 'configuracao/orgao-ato' ||
                                    Route::current()->getPrefix() == 'configuracao/departamento' ||
                                    Route::current()->getPrefix() == 'configuracao/autoridade' ||
                                    Route::current()->getPrefix() == 'configuracao/tipo-ato' ||
                                    Route::current()->getPrefix() == 'configuracao/publicacao-ato' ||
                                    Route::current()->getPrefix() == 'configuracao/forma-publi-ato' ||
                                    Route::current()->getPrefix() == 'configuracao/tamanho-anexo' ||
                                    Route::current()->getPrefix() == 'configuracao/tipo-documento' ||
                                    Route::current()->getPrefix() == 'configuracao/gestao-administrativa'
                                        ? 'active'
                                        : 'collapse' }}">
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/assunto-ato' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.assunto_ato.index') }}">Assuntos
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/classificacao-ato' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.classificacao_ato.index') }}">Classificação do Ato
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/orgao-ato' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.orgao_ato.index') }}">Órgão do Ato
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/departamento' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.departamento.index') }}">Departamento
                                        </a>
                                    </li>
                                    {{-- <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/autoridade' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.autoridade.index') }}">Autoridades
                                        </a>
                                    </li> --}}
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/tipo-ato' ? 'active' : null }}">
                                        <a class="sidebar-link " href="{{ route('configuracao.tipo_ato.index') }}">Tipos de
                                            Ato
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/publicacao-ato' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.publicacao_ato.index') }}">Publicações
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/forma-publi-ato' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.forma_publi_ato.index') }}">Forma de Publicação
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/tamanho-anexo' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.tamanho_anexo.index') }}">Tamanho dos Anexos</a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/tipo-documento' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.tipo_documento.index') }}">Tipo de Documento
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/gestao-administrativa' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.gestao_administrativa.index') }}">Gestão Administrativa
                                        </a>
                                    </li>
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/certificado' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('configuracao.certificado.index') }}">Meu certificado
                                        </a>
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
                                        <span class="avatar"> {{ Auth::user()->pessoa->nome }} - {{ Auth::user()->email }}</span>
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
    <script src="{{asset('js/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.js"></script>
    <script src="{{ asset('select2-4.1.0/dist/js/select2.min.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8-beta.17/inputmask.js" integrity="sha512-XvlcvEjR+D9tC5f13RZvNMvRrbKLyie+LRLlYz1TvTUwR1ff19aIQ0+JwK4E6DCbXm715DQiGbpNSkAAPGpd5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>

    {{-------------------------------- Trowbowyg --------------------------------}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js" integrity="sha512-YJgZG+6o3xSc0k5wv774GS+W1gx0vuSI/kr0E0UylL/Qg/noNspPtYwHPN9q6n59CTR/uhgXfjDXLTRI+uIryg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/langs/pt_br.min.js" integrity="sha512-iJ7snbcZfiZbui/K17AYkBONvjRS1F3V/Y/Ph7n84hptyJUDeXO6rCUX05N5yeY53EUyDotiLn+nK4GXoKXyug==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontfamily/trumbowyg.fontfamily.min.js" integrity="sha512-oATdSCPRZu3qFFyxrZ66ma2QbQybLqpRqwLRp2IQEaIABnEHcs2qDf6UOVA/V5LhBvxFxKCNvyVb/yQfwDkFhQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontsize/trumbowyg.fontsize.min.js" integrity="sha512-eFYo+lmyjqGLpIB5b2puc/HeJieqGVD+b8rviIck2DLUVuBP1ltRVjo9ccmOkZ3GfJxWqEehmoKnyqgQwxCR+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/table/trumbowyg.table.min.js" integrity="sha512-StAj4jlQaB7+Ch81cZyms1l21bLyLjjI6YB2m2UP0cVv6ZEKs5egZYhLTNBU96SylBJEqBquyaAUfFhVUrX20Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/colors/trumbowyg.colors.min.js" integrity="sha512-SHpxBJFbCaHlqGpH13FqtSA+QQkQfdgwtpmcWedAXFCDxAYMgrqj9wbVfwgp9+HgIT6TdozNh2UlyWaXRkiurw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/plugins/fontsize/trumbowyg.fontsize.min.js" integrity="sha512-eFYo+lmyjqGLpIB5b2puc/HeJieqGVD+b8rviIck2DLUVuBP1ltRVjo9ccmOkZ3GfJxWqEehmoKnyqgQwxCR+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- <script>
        $('#perfil_ativo').on('change', function() {
            $('#form-alterar-perfil').submit();
        });
    </script> --}}

    @yield('scripts')

</html>
