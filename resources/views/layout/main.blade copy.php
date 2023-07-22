<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>IDR-Paraná</title>
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
            ->where('ativo', '=', 1)
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
            </a>
            {{-- <a class="sidebar-brand" href="">
                <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.jpg'))) }}"
                alt="" width="40px" height="40px" />
                <span class="align-middle mr-3" style="font-size: .999rem;">IDR-Paraná</span>
            </a> --}}

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Páginas
                </li>

                @if (Auth::user())

                    {{-- Home --}}
                    <li class="sidebar-item {{ Route::current()->uri == 'home' ? 'active' : null }}">
                        <a href="{{ route('home') }}" class="sidebar-link">
                            {{-- <i class="fas fa-address-book"></i> user-circle --}}
                            <i class="fas fa-user-circle"></i>
                            Dados do Usuário
                        </a>
                    </li>



                    @if (Auth::user()->id_tipo_perfil == 3)

                        @if (Auth::user()->temPermissao('Agricultor', 'Listagem*') == 1)
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/agricultor' ? 'active' : null }}">
                                <a href="{{ route('acesso_externo.agricultor.edit') }}" class="sidebar-link">
                                    <i class="fas fa-address-book"></i>
                                    Cadastro
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->temPermissao('Agendamento', 'Listagem*') == 1)
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/atendimento' ? 'active' : null }}">
                                <a href="{{ route('acesso_externo.atendimento.index') }}" class="sidebar-link">
                                    <i class="fas fa-phone"></i>
                                    Atendimentos
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->temPermissao('Chat', 'Listagem*') == 1)
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/chat' ? 'active' : null }}">
                                <a href="{{ route('acesso_externo.chat.index') }}" class="sidebar-link">
                                    <i class="fas fa-comments"></i>
                                    Chat
                                    <span class="badge badge-sidebar-primary">{{ $countVisuMsg != 0 ? $countVisuMsg : '' }}</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->temPermissao('Evento', 'Listagem*') == 1)
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/evento' ? 'active' : null }}">
                                <a href="{{ route('acesso_externo.evento.index') }}" class="sidebar-link">
                                    <i class="fas fa-address-book"></i>
                                    Evento
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->temPermissao('Acervo', 'Listagem*') == 1)
                            <li
                                class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/acervo' ? 'active' : null }}">
                                <a href="{{ route('acesso_externo.acervo.index') }}" class="sidebar-link">
                                    <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    <span>Biblioteca</span>
                                </a>
                            </li>
                        @endif

                    @endif

                    {{-- @if (Auth::user()->temPermissao('Agricultor', 'Listagem*') == 1)
                        <li
                            class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/agricultor' ? 'active' : null }}">
                            <a href="{{ route('acesso_externo.agricultor.edit') }}" class="sidebar-link">
                                <i class="fas fa-address-book"></i>
                                Cadastro
                            </a>
                        </li>
                    @endif --}}

                    {{-- @if (Auth::user()->temPermissao('Agricultor', 'Listagem*') == 1)
                        <li class="sidebar-item">
                            <a href="#cadastro" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-calendar"></i>
                                Cadastro
                            </a>
                            <ul id="cadastro"
                                class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == 'acesso-externo/agricultor' ? 'active' : 'collapse' }}">
                                <li
                                    class="sidebar-item {{ Route::current()->uri == 'acesso-externo/agricultor/edit' ? 'active' : null }}">
                                    <a class="sidebar-link "
                                        href="{{ route('acesso_externo.agricultor.edit') }}">Cliente</a>
                                </li>
                                <li
                                    class="sidebar-item
                                    {{
                                        Route::current()->uri == 'acesso-externo/atendimento/agendamentos' ||
                                        Route::current()->uri == 'acesso-externo/atendimento/acompanhar/{id_agendamento}'
                                    ? 'active' : null }}
                                    ">
                                    <a class="sidebar-link "
                                        href="">Organização</a>
                                </li>
                            </ul>
                        </li>
                    @endif --}}

                    {{-- @if (Auth::user()->temPermissao('Agendamento', 'Listagem*') == 1)
                        <li
                            class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/atendimento' ? 'active' : null }}">
                            <a href="{{ route('acesso_externo.atendimento.index') }}" class="sidebar-link">

                                <i class="fas fa-phone"></i>

                                Atendimentos
                            </a>
                        </li>
                    @endif --}}



                        {{-- <li
                            class="sidebar-item {{ Route::current()->getPrefix() == 'acesso-externo/agendamento' ? 'active' : null }}">
                            <a href="{{ route('acesso_externo.agendamento.index') }}" class="sidebar-link">
                                <i class="fas fa-calendar"></i>
                                Agenda
                            </a>
                        </li> --}}

                    {{-- Cadastro de Pessoa, Agricultor e Organização --}}
                    @if (Auth::user()->temPermissao('Pessoa', 'Listagem') == 1)
                        {{-- <li class="sidebar-item {{ Route::current()->getPrefix() == '/pessoa' ? 'active' : null }}">
                            <a href="{{ route('pessoa.index') }}" class="sidebar-link">
                                <i class="fas fa-address-book"></i>
                                Pessoa
                            </a>
                        </li> --}}
                        <li class="sidebar-item">
                            <a href="#cadastros" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-plus-square"></i>
                                Cadastros
                            </a>
                            <ul id="cadastros"
                                class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/agricultor' || Route::current()->getPrefix() == '/organizacao'
                                    ? 'active' : 'collapse' }}">
                                {{-- <li class="sidebar-item {{
                                Route::current()->getPrefix() == '/pessoa'
                            ? 'active' : null }}">
                                <a class="sidebar-link " href="{{ route('pessoa.index')}}">Pessoa</a>
                            </li> --}}
                                <li
                                    class="sidebar-item {{ Route::current()->getPrefix() == '/agricultor' ? 'active' : null }}">
                                    <a class="sidebar-link " href="{{ route('agricultor.index') }}">Cliente</a>
                                </li>
                                <li
                                    class="sidebar-item {{ Route::current()->getPrefix() == '/organizacao' ? 'active' : null }}">
                                    <a class="sidebar-link " href="{{ route('organizacao.index') }}">Organização</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- @if (Auth::user()->temPermissao('Grupo', 'Listagem') == 1)
                        <li class="sidebar-item">
                            <a href="#grupo_usuario" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-address-book"></i>
                                Grupos de Usuário
                            </a>
                            <ul id="grupo_usuario" class="sidebar-dropdown list-unstyled {{
                                Route::current()->getPrefix() == '/grupo-usuario'
                                ? 'active' : 'collapse'
                            }}">
                                <li class="sidebar-item {{
                                    Route::current()->uri == 'grupo-usuario/index'
                                ? 'active' : null}}">
                                    <a class="sidebar-link " href="{{ route('grupo_usuario.index')}}">Listagem e cadastro</a>
                                </li>
                            </ul>
                        </li>
                    @endif --}}

                    {{-- Gerenciamento --}}
                    @if (Auth::user()->temPermissao('Programa', 'Listagem') == 1 ||
                            Auth::user()->temPermissao('Grupo', 'Listagem') == 1 ||
                            Auth::user()->temPermissao('Processo', 'Listagem') == 1 ||
                            Auth::user()->temPermissao('Processo', 'Listagem*') == 1)
                        <li class="sidebar-item">
                            <a href="#gerenciamento" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-desktop"></i>
                                Gerenciamento
                                <span
                                    class="badge badge-sidebar-primary">{{ $countVisu != 0 ? $countVisu : '' }}</span>
                            </a>
                            <ul id="gerenciamento"
                                class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == 'gerenciamento/programa' ||
                                Route::current()->getPrefix() == 'gerenciamento/grupo-usuario' ||
                                Route::current()->getPrefix() == 'gerenciamento/processo' ||
                                Route::current()->getPrefix() == 'gerenciamento/processo/grupo' ||
                                Route::current()->getPrefix() == 'gerenciamento/processo/anexo' ||
                                Route::current()->getPrefix() == 'gerenciamento/processo/encaminhamento'
                                    ? 'active'
                                    : 'collapse' }}">
                                @if (Auth::user()->temPermissao('Grupo', 'Listagem') == 1)
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'gerenciamento/grupo-usuario' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('gerenciamento.grupo_usuario.index') }}">Grupos de
                                            Usuário</a>
                                    </li>
                                @endif

                                @if (Auth::user()->temPermissao('Programa', 'Listagem') == 1)
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'gerenciamento/programa' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('gerenciamento.programa.index') }}">Programas</a>
                                    </li>
                                @endif

                                @if (Auth::user()->temPermissao('Processo', 'Listagem') == 1 || Auth::user()->temPermissao('Processo', 'Listagem*') == 1)
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'gerenciamento/processo' ||
                                        Route::current()->getPrefix() == 'gerenciamento/processo/grupo' ||
                                        Route::current()->getPrefix() == 'gerenciamento/processo/anexo' ||
                                        Route::current()->getPrefix() == 'gerenciamento/processo/encaminhamento'
                                            ? 'active'
                                            : null }}">
                                        <a class="sidebar-link " href="{{ route('gerenciamento.processo.index') }}">
                                            Processos
                                            <span
                                                class="badge badge-sidebar-primary">{{ $countVisu != 0 ? $countVisu : '' }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Atendimentos --}}
                    @if (Auth::user()->temPermissao('Agendamento', 'Listagem') == 1 ||
                            Auth::user()->temPermissao('HorarioAtendimento', 'Listagem') == 1)
                        <li class="sidebar-item">
                            <a href="#atendimentos" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-phone"></i>
                                Atendimentos
                                <span
                                    class="badge badge-sidebar-primary">{{ $countVisu != 0 ? $countVisu : '' }}</span>
                            </a>
                            <ul id="atendimentos"
                                class="sidebar-dropdown list-unstyled {{ // Route::current()->getPrefix() == 'gerenciamento/programa' ||
                                    // Route::current()->getPrefix() == 'gerenciamento/grupo-usuario' ||
                                    // Route::current()->getPrefix() == 'gerenciamento/processo' ||
                                    // Route::current()->getPrefix() == 'gerenciamento/processo/grupo' ||
                                    Route::current()->getPrefix() == 'atendimento/agendamento' ||
                                    Route::current()->getPrefix() == 'atendimento/data-atendimento'
                                        ? 'active'
                                        : 'collapse' }}">

                                @if (Auth::user()->temPermissao('DataAtendimento', 'Listagem') == 1)
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'atendimento/data-atendimento' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('atendimento.data_atendimento.index') }}">Data de
                                            Atendimento</a>
                                    </li>
                                @endif

                                @if (Auth::user()->temPermissao('Agendamento', 'Listagem') == 1)
                                    <li
                                        class="sidebar-item {{ Route::current()->getPrefix() == 'atendimento/agendamento' ? 'active' : null }}">
                                        <a class="sidebar-link "
                                            href="{{ route('atendimento.agendamento.index') }}">Agendamentos</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Chat --}}
                    @if (Auth::user()->temPermissao('Chat', 'Listagem') == 1)
                        <li class="sidebar-item {{ Route::current()->getPrefix() == '/chat' ? 'active' : null }}">
                            <a href="{{ route('chat.index') }}" class="sidebar-link">
                                <i class="fas fa-comments"></i>
                                Chat
                                <span
                                    class="badge badge-sidebar-primary">{{ $countVisuMsg != 0 ? $countVisuMsg : '' }}</span>
                            </a>
                        </li>
                    @endif

                    {{-- Evento --}}
                    @if (Auth::user()->temPermissao('Evento', 'Listagem') == 1)
                        <li class="sidebar-item">
                            <a href="#evento" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="far fa-calendar-alt"></i><span></span>
                                Evento
                            </a>
                            <ul id="evento"
                                class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/evento' || Route::current()->getPrefix() == 'evento/homologacao' ? 'active' : 'collapse' }}">
                                <li
                                    class="sidebar-item {{ Route::current()->uri == 'evento/index' ? 'active' : null }}">
                                    <a class="sidebar-link" href="{{ route('evento.index') }}">Listagem</a>
                                </li>
                                <li
                                    class="sidebar-item {{ Route::current()->uri == 'evento/homologacao' ? 'active' : null }}">
                                    <a class="sidebar-link"
                                        href="{{ route('evento.homologacao.listarHomologacao') }}">Homologação</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- Acervos --}}
                    @if (Auth::user()->temPermissao('Acervo', 'Listagem') == 1)
                        <li class="sidebar-item">
                            <a href="{{ route('acervo.index') }} " class="sidebar-link ">
                                <i class="fa fa-list-alt" aria-hidden="true"></i>
                                <span>Biblioteca</span>

                            </a>
                        </li>
                    @endif

                    {{-- Importação --}}
                    @if (Auth::user()->temPermissao('Importacao', 'Listagem') == 1 ||
                            Auth::user()->temPermissao('Importacao', 'Cadastro') == 1 ||
                            Auth::user()->temPermissao('Importacao', 'Alteração') == 1 ||
                            Auth::user()->temPermissao('Importacao', 'Relatório') == 1)
                        <li class="sidebar-item">
                            <a href="#importacao" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-fw fa-upload"></i><span></span>
                                Importação
                            </a>
                            <ul id="importacao"
                                class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/importacao' ? 'active' : 'collapse' }}">
                                <li
                                    class="sidebar-item {{ Route::current()->uri == 'importacao' ||
                                    Route::current()->uri == 'importacao/pre-listagem/{id}' ||
                                    Route::current()->uri == 'importacao/{id_importacao}/edit/{id_cadastro}'
                                        ? 'active'
                                        : null }}">
                                    <a class="sidebar-link" href="{{ route('importacao.index') }}">Listagem</a>
                                </li>
                                <li
                                    class="sidebar-item {{ Route::current()->uri == 'importacao/create' ? 'active' : null }}">
                                    <a class="sidebar-link" href="{{ route('importacao.create') }}">Cadastrar</a>
                                </li>
                            </ul>
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
                                    <a class="sidebar-link "
                                        href="{{ route('configuracao.tamanho_anexo.index') }}">Tamanho dos Anexos</a>
                                </li>
                                <li
                                    class="sidebar-item {{ Route::current()->getPrefix() == 'configuracao/tipo-evento' // mudar as rotas e criar a pagina
                                        ? 'active'
                                        : null }}">
                                    <a class="sidebar-link "
                                        href="{{ route('configuracao.tipo_evento.index') }}">Tipos de Evento</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- Perfils e Funcionalidades --}}
                    @if (Auth::user()->temPermissao('Perfil', 'Listagem') == 1)
                        <li class="sidebar-item">
                            <a href="#perfis" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="fas fa-user-cog"></i>
                                Perfis
                            </a>
                            <ul id="perfis"
                                class="sidebar-dropdown list-unstyled {{ Route::current()->getPrefix() == '/perfil-funcionalidade' ? 'active' : 'collapse' }}">
                                <li
                                    class="sidebar-item {{ Route::current()->getPrefix() == '/perfil-funcionalidade' ? 'active' : null }}">
                                    <a class="sidebar-link " href="{{ route('perfil_funcionalidade.index') }}">Perfil
                                        e Funcionalidade</a>
                                </li>
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
                                <li
                                    class="sidebar-item {{ Route::current()->uri == 'usuario/create' ? 'active' : null }}">
                                    <a class="sidebar-link" href="{{ route('usuario.create') }}">Cadastro</a>
                                </li>
                                <li
                                    class="sidebar-item {{ Route::current()->getPrefix() == '/auditoria' ? 'active' : null }}">
                                    <a class="sidebar-link" href="{{ route('auditoria.index') }}">Auditoria</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- @if (Auth::user()->temPermissao('Acervo', 'Listagem') == 1)
                        <li class="sidebar-item">

                            <a href="{{ route('acesso_externo.acervo.indexlog') }} " class="sidebar-link ">
                                <i class="fa fa-list-alt" aria-hidden="true"></i>
                                <span>Acervo desenvolvimento</span>

                            </a>

                        </li>
                    @endif --}}

                    {{-- <li class="sidebar-item">
                    <a href="#evento" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="fas fa-fw fa-calendar"></i><span></span>
                        Eventos
                    </a>
                    <ul id="evento" class="sidebar-dropdown list-unstyled {{
                        Route::current()->uri == 'evento' ||
                        Route::current()->uri == 'evento/create' ||
                        Route::current()->uri == 'evento/inscricao'
                        ? 'active' : 'collapse'
                    }}">
                        <li class="sidebar-item {{ Route::current()->uri == 'evento' || Route::current()->uri == 'evento/create' ? 'active' : null}}">
                            <a class="sidebar-link" href="{{ route('evento.index')}}">Listagem e Cadastro</a>
                        </li>
                        <li class="sidebar-item {{ Route::current()->uri == 'evento/inscricao' ? 'active' : null}}">
                            <a class="sidebar-link " href="{{ route('evento.inscricao')}}">Inscrição</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="#atendimento" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="fas fa-fw fa-phone"></i><span></span>
                        Atendimentos
                    </a>
                    <ul id="atendimento" class="sidebar-dropdown list-unstyled {{
                        Route::current()->uri == 'atendimento/horario-atendimento' ||
                        Route::current()->uri == 'atendimento/agendamentos'
                        ? 'active' : 'collapse'
                    }}">
                        <li class="sidebar-item {{ Route::current()->uri == 'atendimento/horario-atendimento' ? 'active' : null}}">
                            <a class="sidebar-link" href="{{ route('atendimento.horario_atendimento.index')}}">Listagem e Cadastro</a>
                        </li>
                        <li class="sidebar-item {{ Route::current()->uri == 'atendimento/agendamentos' ? 'active' : null}}">
                            <a class="sidebar-link " href="{{ route('atendimento.agendamentos.index')}}">Agendamentos</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="#coord_e_fig" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="fas fa-fw fa-map-marked"></i><span></span>
                        Coodenadas e figuras geográficas
                    </a>
                    <ul id="coord_e_fig" class="sidebar-dropdown list-unstyled {{
                        Route::current()->uri == 'ponto_geografico/create' ||
                        Route::current()->uri == 'figura_geografica/create' ||
                        Route::current()->uri == 'figura_geografica/edit/{id}' ||
                        Route::current()->uri == 'figura_geografica/show/{id}'
                        ? 'active' : 'collapse'
                    }}">
                        <li class="sidebar-item {{ Route::current()->uri == 'ponto_geografico/create' ? 'active' : null }}">
                            <a class="sidebar-link" href="{{ route('ponto_geografico.create')}}">Coordenadas</a>
                        </li>
                        <li class="sidebar-item {{
                        Route::current()->uri == 'figura_geografica/create' ||
                        Route::current()->uri == 'figura_geografica/edit/{id}' ||
                        Route::current()->uri == 'figura_geografica/show/{id}'
                        ? 'active' : null }}">
                            <a class="sidebar-link" href="{{ route('figura_geografica.create')}}">Figuras</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item {{ Route::current()->uri == 'processo/create' ? 'active' : null }}">
                    <a href="{{ route('processo.create') }}" class="sidebar-link">
                        <i class="fas fa-plus-square"></i>
                        Processos
                    </a>
                </li>

                <li class="sidebar-item {{ Route::current()->uri == 'anexo/create' ? 'active' : null }}">
                    <a href="{{ route('anexo.create') }}" class="sidebar-link">
                        <i class="fas fa-paperclip"></i>
                        Anexos
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('chat.create') }}" class="sidebar-link {{ Route::current()->uri == 'chat/create' ? 'active' : null }}">
                        <i class="fas fa-comments"></i>
                        Chat
                        <span class="badge badge-sidebar-primary">{{ $countVisuMsg != 0 ? $countVisuMsg : '' }}</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#importacao" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="fas fa-fw fa-upload"></i><span></span>
                        Importação
                    </a>
                    <ul id="importacao" class="sidebar-dropdown list-unstyled {{
                        Route::current()->uri == 'importacao' ||
                        Route::current()->uri == 'importacao/create'
                        ? 'active' : 'collapse'
                    }}">
                        <li class="sidebar-item {{ Route::current()->uri == 'importacao' ? 'active' : null }}">
                            <a class="sidebar-link" href="{{ route('importacao.index')}}">Listagem</a>
                        </li>
                        <li class="sidebar-item {{ Route::current()->uri == 'importacao/create' ? 'active' : null }}">
                            <a class="sidebar-link" href="{{ route('importacao.create')}}">Cadastrar</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="#usuarios" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="fas fa-users"></i>
                        Gestores
                    </a>
                    <ul id="usuarios" class="sidebar-dropdown list-unstyled {{
                        Route::current()->uri == 'gestor' ||
                        Route::current()->uri == 'gestor/create'  ||
                        Route::current()->uri == 'gestor/edit/{id}'
                        ? 'active' : 'collapse'
                    }}">
                        <li class="sidebar-item {{
                                Route::current()->uri == 'gestor' ||
                                Route::current()->uri == 'gestor/edit/{id}'
                            ? 'active' : null }}">
                            <a class="sidebar-link" href="{{ route('gestor.index')}}">Listagem</a>
                        </li>
                        <li class="sidebar-item {{ Route::current()->uri == 'gestor/create' ? 'active' : null }}">
                            <a class="sidebar-link" href="{{ route('gestor.create')}}">Cadastrar</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('aviso.create') }}" class="sidebar-link {{ Route::current()->uri == 'aviso/create' ? 'active' : null }}">
                        <i class="fas fa-envelope"></i>
                        Avisos
                        <span class="badge badge-sidebar-primary">{{ $countVisu != 0 ? $countVisu : '' }}</span>
                    </a>
                </li> --}}

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
                <ul class="navbar-nav">
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
                </ul>

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
