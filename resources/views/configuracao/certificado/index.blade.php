@extends('layout.main')

@section('content')

    <style>
        .attr {
            font-weight: bold;
            color: black;
        }
    </style>

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Meu certitficado</h1>

    @include('sweetalert::alert')

    <div class="card">
        <div class="card-body">
            <div class="col-md-12">
                <h5>Gerencie seu Certificado Digital A1 para realizar assinatura eletrônica de documentos pelo sistema</h5>
                <hr>
            </div>
            @if ($certificado)

                {{-- modal de exclusão de certificado --}}
                <div class="modal fade" id="modalDestroy" tabindex="-1" role="dialog" aria-labelledby="modalDestroyLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('configuracao.certificado.destroy', $certificado->id) }}">
                                @csrf
                                @method('POST')
                                <div class="modal-header btn-danger">
                                    <h5 class="modal-title text-center" id="modalDestroyLabel" style="color: white">
                                        Excluir certificado do sistema?
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <div class="col-md-12">
                                        Tem certeza que deseja excluir o certificado do sistema?
                                        Não é possível realizar assinatura eletrônica pelo sistema sem um certificado.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                                    </button>
                                    <button type="submit" class="button_submit btn btn-danger">Excluir</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="temCertificado col-md-12">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 my-0 py-0">
                                <h4><span class="badge badge-success">Você possui um certificado:</span></h4>
                            </div>
                            <div class="col-md-12 mb-3">
                                <ul>
                                    <li><span class="attr">Nome original do arquivo:</span> {{ $certificado->nome_original }}</li>
                                    <li><span class="attr">Nome do certificado:</span> {{ $certificado->nome_cert }}</li>
                                    <li><span class="attr">Data de validade:</span> {{ date('d/m/Y', strtotime($certificado->data_validade)) }}</li>
                                    <li><span class="attr">Tipo:</span> {{ $certificado->tipo }}</li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <a data-toggle="modal" data-target="#modalDestroy" class="btn btn-danger">Excluir certificado</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="naoTemCertificado col-md-12">
                    <form action="{{ route('configuracao.certificado.store') }}" method="POST"
                        class="form_prevent_multiple_submits" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 my-0 py-0">
                                    <h4><span class="badge badge-secondary">Adicionar certificado:</span></h4>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="arquivo">Arquivo</label>
                                    <input class="w-100 @error('arquivo') is-invalid @enderror"
                                        type="file" accept=".pfx,.p12" name="arquivo" required>
                                    @error('arquivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="password">Senha</label>
                                    <input class="form-control @error('password') is-invalid @enderror"
                                        type="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('scripts')

@endsection
