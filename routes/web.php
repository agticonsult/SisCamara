<?php

use App\Http\Controllers\AtoController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\ConfirmacaoEmailController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\FileSizeController;
use App\Http\Controllers\FinalidadeGrupoController;
use App\Http\Controllers\FotoPerfilController;
use App\Http\Controllers\FuncionalidadeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PerfilFuncionalidadeController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\TipoFilesizeController;
use App\Http\Controllers\UserController;
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

//Login
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/autenticacao', [LoginController::class, 'autenticacao'])->name('login.autenticacao');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

//registrar usuário
Route::get('registrar-usuario', [RegistrarController::class, 'registrar'])->name('registrar_usuario');
Route::post('registrar-store', [RegistrarController::class, 'registrarStore'])->name('registrar_store');

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
    Route::post('/foto', [FotoPerfilController::class, 'store'])->name('upload_foto');

    // Ato
    Route::group(['prefix' => '/ato', 'as' => 'ato.'], function() {
        Route::get('/index', [AtoController::class, 'index'])->name('index');
        Route::get('/create', [AtoController::class, 'create'])->name('create');
        Route::post('/store', [AtoController::class, 'store'])->name('store');
        Route::get('/edit', [AtoController::class, 'edit'])->name('edit');
        Route::post('/update', [AtoController::class, 'update'])->name('update');
        Route::post('/destroy', [AtoController::class, 'destroy'])->name('destroy');
    });



    // Usuarios/Clientes
    Route::group(['prefix' => '/usuario', 'as' => 'usuario.'], function() {
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
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
    Route::group(['prefix' => '/pessoa', 'as' => 'pessoa.'], function() {
        Route::get('/index', [PessoaController::class, 'index'])->name('index');
        Route::get('/create', [PessoaController::class, 'create'])->name('create');
        Route::post('/store', [PessoaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PessoaController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [PessoaController::class, 'update'])->name('update');
    });

    // Configuração
    Route::group(['prefix' => '/configuracao', 'as' => 'configuracao.'], function() {

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

//AJAX
// Route::get('/busca-municipio/{id}', [MunicipioController::class, 'buscaMunicipios'])->name('municipio.busca-municipios');
