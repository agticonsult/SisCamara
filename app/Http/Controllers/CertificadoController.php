<?php

namespace App\Http\Controllers;

use App\Http\Requests\CertificadoRequest;
use App\Models\Certificado;
use App\Services\ErrorLogService;
use App\Utils\CertificadoUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;

class CertificadoController extends Controller
{
    public function index()
    {
        try {
            $certificado = Certificado::where('id_user', Auth::user()->id)->first();

            return view('configuracao.certificado.index', compact('certificado'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'CertificadoController', 'index');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function store(CertificadoRequest $request)
    {
        try {
            $arquivo = $request->file('arquivo');

            if (!$arquivo){
                Alert::toast('O campo do arquivo está vazio!','error');
                return redirect()->back();
            }

            if (!$arquivo->isValid()) {
                Alert::toast('Arquivo inválido.','error');
                return redirect()->back();
            }

            $extensao = $arquivo->getClientOriginalExtension();
            $nome_original = $arquivo->getClientOriginalName();

            if ($extensao != 'pfx' &&
                $extensao != 'p12') {
                Alert::toast('Extensão não permitida! As extensões permitidas para este arquivo são .pfx e .p12 .','error');
                return redirect()->back();
            }

            $certificadoUtil = CertificadoUtil::getCertificateValidity($arquivo, $request->password);
            $resultadoUtil = $certificadoUtil->getOriginalContent();

            if ($certificadoUtil->getStatusCode() != 200) {
                Alert::toast($resultadoUtil['error'],'error');
                return redirect()->back();
            }

            $nome_hash = Uuid::uuid4();
            $nome_hash = $nome_hash . '.' . $extensao;
            $upload = $arquivo->storeAs('public/Certificado/', $nome_hash);

            if (!$upload) {
                Alert::toast('Houve uma falha no upload do arquivo, tente novamenta ou contate administrador do sistema.','error');
                return redirect()->back();
            }

            $certificado = new Certificado();
            $certificado->nome_original = $nome_original;
            $certificado->nome_hash = $nome_hash;
            $certificado->diretorio = 'public/Certificado/';
            $certificado->password = Hash::make($request->password);
            $certificado->data_validade = $resultadoUtil['data_validade'];
            $certificado->tipo = $resultadoUtil['tipo'];
            $certificado->nome_cert = $resultadoUtil['nome_cert'];
            $certificado->id_user = Auth::user()->id;
            $certificado->save();

            Alert::toast('Seu certificado foi salvo com sucesso!', 'success');
            return redirect()->back();

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'CertificadoController', 'store');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {

            $certificado = Certificado::findOrFail($id);

            if (Storage::delete($certificado->diretorio . $certificado->nome_hash)) {
                $certificado->delete();

                Alert::toast('Exclusão realizado com sucesso!', 'success');
                return redirect()->back();
            }else{
                Alert::toast('A exclusão do arquivo falhou, tente novamente ou contate o administrador do sistema!', 'error');
                return redirect()->back();
            }

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'CertificadoController', 'destroy');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }
}
