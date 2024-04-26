<?php

use App\Http\Controllers\AgentePoliticoController;
use App\Http\Controllers\AnexoAtoController;
use App\Http\Controllers\AssuntoAtoController;
use App\Http\Controllers\AtoController;
use App\Http\Controllers\AtoPublicoController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\ConfirmacaoEmailController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ExportAtoController;
use App\Http\Controllers\AutoridadeController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ProposicaoController;
use App\Http\Controllers\FileSizeController;
use App\Http\Controllers\FinalidadeGrupoController;
use App\Http\Controllers\FotoPerfilController;
use App\Http\Controllers\FuncionalidadeController;
use App\Http\Controllers\GerenciamentoVotacaoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\LegislaturaController;
use App\Http\Controllers\ModeloProposicaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PerfilFuncionalidadeController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\PleitoEleitoralController;
use App\Http\Controllers\ProposicaoPublicoController;
use App\Http\Controllers\PublicacaoAtoController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\ReparticaoController;
use App\Http\Controllers\TipoAtoController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\TipoFilesizeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VereadorController;
use App\Http\Controllers\VereadorVotacaoController;
use App\Http\Controllers\VotacaoEletronicaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

//Inicio
Route::get('/', [InicioController::class, 'index'])->name('index');
Route::get('/login', [InicioController::class, 'login'])->name('login');


//Login
Route::post('/autenticacao', [LoginController::class, 'autenticacao'])->name('login.autenticacao');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

//registrar usuário
Route::get('selecionar-pessoa', [RegistrarController::class, 'selecionarPessoa'])->name('selecionar_pessoa');
Route::get('registrar-pessoa-fisica', [RegistrarController::class, 'registrarPessoaFisica'])->name('registrar_pessoa_fisica');
Route::post('pessoa-fisica-store', [RegistrarController::class, 'pessoaFisicaStore'])->name('pessoa_fisica_store');
Route::post('pessoa-juridica-store', [RegistrarController::class, 'pessoaJuridicaStore'])->name('pessoa_juridica_store');
Route::get('registrar-pessoa-juridica', [RegistrarController::class, 'registrarPessoaJuridica'])->name('registrar_pessoa_juridica');

//Alteração de senha
Route::get('/passwordReset1', [PasswordResetController::class, 'passwordReset1'])->name('passwordReset1');
Route::post('/passwordReset2', [PasswordResetController::class, 'passwordReset2'])->name('passwordReset2');
Route::get('/passwordReset3/{id}', [PasswordResetController::class, 'passwordReset3'])->name('passwordReset3');
Route::post('/passwordReset4/{id}', [PasswordResetController::class, 'passwordReset4'])->name('passwordReset4'); //atualização de senha

//Confirmação de e-mail, após a realização do cadastro
Route::get('/reenviar-link', [ConfirmacaoEmailController::class, 'encaminharLink'])->name('reenviar_link');
Route::post('/link-encaminhado', [ConfirmacaoEmailController::class, 'linkEncaminhado'])->name('link_encaminhado');
Route::get('/confirmacao-email/{id}', [ConfirmacaoEmailController::class, 'confirmacaoEmail'])->name('confirmacao_email');

Route::group(['middleware' => 'auth'], function () {

    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/alterar-perfil', [HomeController::class, 'alterarPerfil'])->name('home.alterarPerfil');
    Route::post('/update/{id}', [HomeController::class, 'update'])->name('home.update');
    Route::post('/update-pj/{id}', [HomeController::class, 'updatePj'])->name('home.updatePj');
    Route::post('/foto', [FotoPerfilController::class, 'store'])->name('upload_foto');

    // Ato
    Route::group(['prefix' => '/ato', 'as' => 'ato.'], function() {
        Route::get('/', [AtoController::class, 'index'])->name('index');
        Route::get('/show/{id}', [AtoController::class, 'show'])->name('show');
        Route::get('/create', [AtoController::class, 'create'])->name('create');
        Route::post('/store', [AtoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AtoController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [AtoController::class, 'update'])->name('update');
        Route::post('/destroy/{id}', [AtoController::class, 'destroy'])->name('destroy');

        // Dados Gerais
        Route::group(['prefix' => '/dados-gerais', 'as' => 'dados_gerais.'], function() {
            Route::get('/edit/{id}', [AtoController::class, 'editDadosGerais'])->name('edit');
            Route::post('/update/{id}', [AtoController::class, 'updateDadosGerais'])->name('update');
        });

        // Corpo do texto
        Route::group(['prefix' => '/corpo-do-texto', 'as' => 'corpo_texto.'], function() {
            Route::get('/edit/{id}', [AtoController::class, 'editCorpoTexto'])->name('edit');
            Route::post('/alterar-linha/{id}', [AtoController::class, 'alterarLinha'])->name('alterarLinha');
        });

        // Corpo do texto
        Route::group(['prefix' => '/anexos', 'as' => 'anexos.'], function() {
            Route::post('/store/{id}', [AnexoAtoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AnexoAtoController::class, 'edit'])->name('edit');
            Route::post('/destroy/{id}', [AnexoAtoController::class, 'destroy'])->name('destroy');
        });

        // PDF
        Route::group(['prefix' => '/export', 'as' => 'export.'], function() {

            // Original
            Route::group(['prefix' => '/original', 'as' => 'original.'], function() {
                Route::get('/pdf/{id}', [ExportAtoController::class, 'pdfOriginal'])->name('pdf');
                Route::get('/html/{id}', [ExportAtoController::class, 'htmlOriginal'])->name('html');
                Route::get('/texto/{id}', [ExportAtoController::class, 'textoOriginal'])->name('texto');
                Route::get('/doc/{id}', [ExportAtoController::class, 'docOriginal'])->name('doc');
            });

            // Consolidada
            Route::group(['prefix' => '/consolidada', 'as' => 'consolidada.'], function() {
                Route::get('/pdf/{id}', [ExportAtoController::class, 'pdfConsolidada'])->name('pdf');
                Route::get('/html/{id}', [ExportAtoController::class, 'htmlConsolidada'])->name('html');
                Route::get('/texto/{id}', [ExportAtoController::class, 'textoConsolidada'])->name('texto');
                Route::get('/doc/{id}', [ExportAtoController::class, 'docConsolidada'])->name('doc');
            });

            // Compilada
            Route::group(['prefix' => '/compilada', 'as' => 'compilada.'], function() {
                Route::get('/pdf/{id}', [ExportAtoController::class, 'pdfCompilada'])->name('pdf');
                Route::get('/html/{id}', [ExportAtoController::class, 'htmlCompilada'])->name('html');
                Route::get('/texto/{id}', [ExportAtoController::class, 'textoCompilada'])->name('texto');
                Route::get('/doc/{id}', [ExportAtoController::class, 'docCompilada'])->name('doc');
            });

        });

    });

    // Processo Legilativo
    Route::group(['prefix' => '/processo-legislativo', 'as' => 'processo_legislativo.'], function() {

        // Legislatura
        Route::group(['prefix' => '/legislatura', 'as' => 'legislatura.'], function() {
            Route::get('/index', [LegislaturaController::class, 'index'])->name('index');
            Route::post('/store', [LegislaturaController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [LegislaturaController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [LegislaturaController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [LegislaturaController::class, 'destroy'])->name('destroy');
            Route::get('/get/{id}', [LegislaturaController::class, 'get'])->name('get');
        });

        // Pleito Eleitoral
        Route::group(['prefix' => '/pleito-eleitoral', 'as' => 'pleito_eleitoral.'], function() {
            Route::get('/index', [PleitoEleitoralController::class, 'index'])->name('index');
            Route::get('/create', [PleitoEleitoralController::class, 'create'])->name('create');
            Route::post('/store', [PleitoEleitoralController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PleitoEleitoralController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [PleitoEleitoralController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [PleitoEleitoralController::class, 'destroy'])->name('destroy');
            Route::get('/get/{id}', [PleitoEleitoralController::class, 'get'])->name('get');

            // Cargo Eletivo
            Route::group(['prefix' => '/cargo-eletivo', 'as' => 'cargo_eletivo.'], function() {
                Route::post('/destroy/{id}', [PleitoEleitoralController::class, 'destroyCargoEletivo'])->name('destroy');
            });
        });

    });

    // Proposição
    Route::group(['prefix' => '/proposicao', 'as' => 'proposicao.'], function() {
        Route::get('/index', [ProposicaoController::class, 'index'])->name('index');
        Route::get('/show/{id}', [ProposicaoController::class, 'show'])->name('show');
        Route::get('/create', [ProposicaoController::class, 'create'])->name('create');
        Route::post('/store', [ProposicaoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ProposicaoController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [ProposicaoController::class, 'update'])->name('update');
        Route::post('/destroy/{id}', [ProposicaoController::class, 'destroy'])->name('destroy');

        // Dados Gerais
        Route::group(['prefix' => '/modelo', 'as' => 'modelo.'], function() {
            Route::get('/index', [ModeloProposicaoController::class, 'index'])->name('index');
            Route::get('/create', [ModeloProposicaoController::class, 'create'])->name('create');
            Route::post('/store', [ModeloProposicaoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ModeloProposicaoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [ModeloProposicaoController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [ModeloProposicaoController::class, 'destroy'])->name('destroy');
            Route::get('/get/{id}', [ModeloProposicaoController::class, 'get'])->name('get');
        });

    });

    // Reparticao
    Route::group(['prefix' => '/reparticao', 'as' => 'reparticao.'], function() {
        Route::get('/index', [ReparticaoController::class, 'index'])->name('index');
        Route::get('/create', [ReparticaoController::class, 'create'])->name('create');
        Route::post('/store', [ReparticaoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ReparticaoController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [ReparticaoController::class, 'update'])->name('update');
        Route::post('/destroy/{id}', [ReparticaoController::class, 'destroy'])->name('destroy');
    });

    // Documento
    Route::group(['prefix' => '/documento', 'as' => 'documento.'], function() {
        Route::get('/', [DocumentoController::class, 'index'])->name('index');
        Route::get('/create', [DocumentoController::class, 'create'])->name('create');
        // Route::get('/acompanhar-doc/{id}', [DocumentoController::class, 'acompanharDoc'])->name('acompanharDoc');
        Route::post('/store', [DocumentoController::class, 'store'])->name('store');
        Route::get('/show/{id}', [DocumentoController::class, 'show'])->name('show');
        Route::get('/obter-anexo/{id_anexo}', [DocumentoController::class, 'obterAnexo'])->name('obterAnexo');
        Route::get('/edit/{id}', [DocumentoController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [DocumentoController::class, 'update'])->name('update');
        Route::get('/get-departamentos/{id}', [DocumentoController::class, 'getDepartamentos'])->name('getDepartamentos');
        Route::post('/aprovar/{id}/{id_tipo_workflow}', [DocumentoController::class, 'aprovar'])->name('aprovar');
        Route::post('/reprovar/{id}', [DocumentoController::class, 'reprovar'])->name('reprovar');
        Route::post('/finalizar/{id}', [DocumentoController::class, 'finalizar'])->name('finalizar');
        Route::post('/destroy/{id}', [DocumentoController::class, 'destroy'])->name('destroy');
    });

    // Votação Eletrônica
    Route::group(['prefix' => '/votacao-eletronica', 'as' => 'votacao_eletronica.'], function() {
        Route::get('/index', [VotacaoEletronicaController::class, 'index'])->name('index');
        Route::get('/create', [VotacaoEletronicaController::class, 'create'])->name('create');
        Route::post('/store', [VotacaoEletronicaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [VotacaoEletronicaController::class, 'edit'])->name('edit');
        Route::get('/resultado/{id}', [VotacaoEletronicaController::class, 'resultado'])->name('resultado');
        Route::post('/update/{id}', [VotacaoEletronicaController::class, 'update'])->name('update');
        Route::post('/destroy/{id}', [VotacaoEletronicaController::class, 'destroy'])->name('destroy');

        Route::group(['prefix' => '/gerenciamento', 'as' => 'gerenciamento.'], function() {
            Route::get('/gerenciar/{id}', [GerenciamentoVotacaoController::class, 'gerenciar'])->name('gerenciar');
            Route::get('/iniciar-votacao/{id}', [GerenciamentoVotacaoController::class, 'iniciarVotacao'])->name('iniciarVotacao');
            Route::get('/pausar-votacao/{id}', [GerenciamentoVotacaoController::class, 'pausarVotacao'])->name('pausarVotacao');
            Route::get('/encerrar-votacao/{id}', [GerenciamentoVotacaoController::class, 'encerrarVotacao'])->name('encerrarVotacao');
        });

        Route::group(['prefix' => '/vereador', 'as' => 'vereador.'], function() {
            Route::get('/index', [VereadorVotacaoController::class, 'index'])->name('index');
            Route::get('/votacao/{id}', [VereadorVotacaoController::class, 'votacao'])->name('votacao');
            Route::get('/liberar-votacao/{id}', [VereadorVotacaoController::class, 'liberarVotacao'])->name('liberarVotacao');
            Route::post('/votar/{id}', [VereadorVotacaoController::class, 'votar'])->name('votar');

        });
        // // Dados Gerais
        // Route::group(['prefix' => '/modelo', 'as' => 'modelo.'], function() {
        //     Route::get('/index', [ModeloProposicaoController::class, 'index'])->name('index');
        //     Route::get('/create', [ModeloProposicaoController::class, 'create'])->name('create');
        //     Route::post('/store', [ModeloProposicaoController::class, 'store'])->name('store');
        //     Route::get('/edit/{id}', [ModeloProposicaoController::class, 'edit'])->name('edit');
        //     Route::post('/update/{id}', [ModeloProposicaoController::class, 'update'])->name('update');
        //     Route::post('/destroy/{id}', [ModeloProposicaoController::class, 'destroy'])->name('destroy');
        //     Route::get('/get/{id}', [ModeloProposicaoController::class, 'get'])->name('get');
        // });

    });

    // Agente político
    Route::group(['prefix' => '/agente-politico', 'as' => 'agente_politico.'], function() {
        Route::get('/', [AgentePoliticoController::class, 'index'])->name('index');
        Route::get('/show/{id}', [AgentePoliticoController::class, 'show'])->name('show');
        Route::get('/create', [AgentePoliticoController::class, 'create'])->name('create');
        Route::get('/novo-agente-politico', [AgentePoliticoController::class, 'novoAgentePolitico'])->name('novo_agente_politico');
        Route::get('/vincular', [AgentePoliticoController::class, 'vincularUsuario'])->name('vincularUsuario');
        Route::post('/store', [AgentePoliticoController::class, 'store'])->name('store');
        Route::post('/store-vincular', [AgentePoliticoController::class, 'storeVincular'])->name('storeVincular');
        Route::get('/edit/{id}', [AgentePoliticoController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [AgentePoliticoController::class, 'update'])->name('update');
        Route::post('/destroy/{id}', [AgentePoliticoController::class, 'destroy'])->name('destroy');
    });

    // Usuarios/Clientes
    Route::group(['prefix' => '/usuario', 'as' => 'usuario.'], function() {
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/desbloquear/{id}', [UserController::class, 'desbloquear'])->name('desbloquear');
        Route::post('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        // Route::get('/edit-perfil/{id}', [UserController::class, 'editPerfil'])->name('editPerfil');
        Route::post('/desativa-perfil/{id}', [UserController::class, 'desativaPerfil'])->name('desativaPerfil');
    });

    // Audits
    Route::group(['prefix' => '/auditoria', 'as' => 'auditoria.'], function() {
        Route::get('/index', [AuditController::class, 'index'])->name('index');
        Route::any('/buscar', [AuditController::class, 'buscar'])->name('buscar');
    });

    // Pessoa
    // Route::group(['prefix' => '/pessoa', 'as' => 'pessoa.'], function() {
    //     Route::get('/index', [PessoaController::class, 'index'])->name('index');
    //     Route::get('/create', [PessoaController::class, 'create'])->name('create');
    //     Route::post('/store', [PessoaController::class, 'store'])->name('store');
    //     Route::get('/edit/{id}', [PessoaController::class, 'edit'])->name('edit');
    //     Route::post('/update/{id}', [PessoaController::class, 'update'])->name('update');
    // });

    // Configuração
    Route::group(['prefix' => '/configuracao', 'as' => 'configuracao.'], function() {

        //Departamento
        Route::group(['prefix' => '/departamento', 'as' => 'departamento.'], function() {
            Route::get('/index', [DepartamentoController::class, 'index'])->name('index');
            // Route::get('/create', [DepartamentoController::class, 'create'])->name('create');
            Route::post('/store', [DepartamentoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [DepartamentoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [DepartamentoController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [DepartamentoController::class, 'destroy'])->name('destroy');
            Route::post('/desvincular-usuario/{id}', [DepartamentoController::class, 'desvincularUsuario'])->name('desvincularUsuario');
        });

        //Assunto do Ato
        Route::group(['prefix' => '/assunto-ato', 'as' => 'assunto_ato.'], function() {
            Route::get('/index', [AssuntoAtoController::class, 'index'])->name('index');
            Route::get('/create', [AssuntoAtoController::class, 'create'])->name('create');
            Route::post('/store', [AssuntoAtoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AssuntoAtoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [AssuntoAtoController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [AssuntoAtoController::class, 'destroy'])->name('destroy');
        });

        //Autoridades
        Route::group(['prefix' => '/autoridade', 'as' => 'autoridade.'], function() {
            Route::get('/index', [AutoridadeController::class, 'index'])->name('index');
            Route::get('/create', [AutoridadeController::class, 'create'])->name('create');
            Route::post('/store', [AutoridadeController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AutoridadeController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [AutoridadeController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [AutoridadeController::class, 'destroy'])->name('destroy');
        });

        //Tipos de Ato
        Route::group(['prefix' => '/tipo-ato', 'as' => 'tipo_ato.'], function() {
            Route::get('/index', [TipoAtoController::class, 'index'])->name('index');
            Route::get('/create', [TipoAtoController::class, 'create'])->name('create');
            Route::post('/store', [TipoAtoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [TipoAtoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [TipoAtoController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [TipoAtoController::class, 'destroy'])->name('destroy');
        });

        //Publicação do Ato
        Route::group(['prefix' => '/publicacao-ato', 'as' => 'publicacao_ato.'], function() {
            Route::get('/index', [PublicacaoAtoController::class, 'index'])->name('index');
            Route::get('/create', [PublicacaoAtoController::class, 'create'])->name('create');
            Route::post('/store', [PublicacaoAtoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PublicacaoAtoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [PublicacaoAtoController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [PublicacaoAtoController::class, 'destroy'])->name('destroy');
        });

        // Finalidade dos Grupos de Usuário
        Route::group(['prefix' => '/finalidade-grupo', 'as' => 'finalidade_grupo.'], function() {
            Route::get('/index', [FinalidadeGrupoController::class, 'index'])->name('index');
            Route::get('/create', [FinalidadeGrupoController::class, 'create'])->name('create');
            Route::post('/store', [FinalidadeGrupoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [FinalidadeGrupoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [FinalidadeGrupoController::class, 'update'])->name('update');
        });

        // tamanho dos anexos
        Route::group(['prefix' => '/tamanho-anexo', 'as' => 'tamanho_anexo.'], function() {
            Route::get('/index', [FilesizeController::class, 'index'])->name('index');
            Route::post('/update', [FileSizeController::class, 'update'])->name('update');
        });

        //Tipo de documento
        Route::group(['prefix' => '/tipo-documento', 'as' => 'tipo_documento.'], function() {
            Route::get('/index', [TipoDocumentoController::class, 'index'])->name('index');
            Route::get('/create', [TipoDocumentoController::class, 'create'])->name('create');
            Route::post('/store', [TipoDocumentoController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [TipoDocumentoController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [TipoDocumentoController::class, 'update'])->name('update');
            // Route::post('/destroy/{id}', [TipoDocumentoController::class, 'destroy'])->name('destroy');
        });

    });

    // Perfil
    Route::group(['prefix' => '/perfil', 'as' => 'perfil.'], function() {
        // Route::get('/index', [PerfilController::class, 'index'])->name('index');
        // Route::get('/create', [PerfilController::class, 'create'])->name('create');
        Route::post('/store', [PerfilController::class, 'store'])->name('store');
        Route::get('/funcionalidades/{id}', [PerfilController::class, 'funcionalidades'])->name('funcionalidades');
        // Route::get('/edit/{id}', [PerfilController::class, 'edit'])->name('edit');
        // Route::post('/update/{id}', [PerfilController::class, 'update'])->name('update');
    });

    // Funcionalidade
    Route::group(['prefix' => '/funcionalidade', 'as' => 'funcionalidade.'], function() {
        // Route::get('/index', [PerfilController::class, 'index'])->name('index');
        // Route::get('/create', [PerfilController::class, 'create'])->name('create');
        Route::post('/store', [FuncionalidadeController::class, 'store'])->name('store');
        // Route::get('/edit/{id}', [PerfilController::class, 'edit'])->name('edit');
        // Route::post('/update/{id}', [PerfilController::class, 'update'])->name('update');
    });

    // Perfil e Funcionalidade
    Route::group(['prefix' => '/perfil-funcionalidade', 'as' => 'perfil_funcionalidade.'], function() {
        Route::get('/index', [PerfilFuncionalidadeController::class, 'index'])->name('index');
        Route::get('/create', [PerfilFuncionalidadeController::class, 'create'])->name('create');
        Route::post('/store', [PerfilFuncionalidadeController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PerfilFuncionalidadeController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [PerfilFuncionalidadeController::class, 'update'])->name('update');
        Route::post('/inativar-funcionalidade/{id}', [PerfilFuncionalidadeController::class, 'inativarFuncionalidade'])->name('inativarFuncionalidade');
    });

});

// Web Pública
Route::group(['prefix' => '/web-publica', 'as' => 'web_publica.'], function() {

    // Ato
    Route::group(['prefix' => '/ato', 'as' => 'ato.'], function() {
        Route::get('/index', [AtoPublicoController::class, 'index'])->name('index');
        Route::get('/show/{id}', [AtoPublicoController::class, 'show'])->name('show');

        Route::group(['prefix' => '/busca', 'as' => 'busca.'], function() {
            Route::any('/livre', [AtoPublicoController::class, 'buscaLivre'])->name('livre');
            Route::any('/especifica', [AtoPublicoController::class, 'buscaEspecifica'])->name('especifica');
        });
    });

    //Proposicao
    Route::group(['prefix' => '/proposicao', 'as' => 'proposicao.'], function() {
        Route::get('/index', [ProposicaoPublicoController::class, 'index'])->name('index');
        Route::get('/show/{id}', [ProposicaoPublicoController::class, 'show'])->name('show');

        // Route::group(['prefix' => '/busca', 'as' => 'busca.'], function() {
        //     Route::any('/livre', [AtoPublicoController::class, 'buscaLivre'])->name('livre');
        //     Route::any('/especifica', [AtoPublicoController::class, 'buscaEspecifica'])->name('especifica');
        // });
    });

    // Votação Eletrônica
    Route::group(['prefix' => '/votacao-eletronica', 'as' => 'votacao_eletronica.'], function() {
        Route::get('/index', [VotacaoEletronicaController::class, 'indexPublico'])->name('indexPublico');
        Route::get('/resultado/{id}', [VotacaoEletronicaController::class, 'resultadoPublico'])->name('resultadoPublico');
    });
});

//AJAX
// Route::get('/busca-municipio/{id}', [MunicipioController::class, 'buscaMunicipios'])->name('municipio.busca-municipios');
