<style>
    .btn-tramitar {
        width: 90% !important;
        height: 2.75rem !important;
        justify-content: center;
        align-items: center;
        text-align: center;
        display: flex;
    }
</style>

<div class="card-body">
    <div class="col-md-12">
        <div class="row">
            @if ($aptoAprovar)
                <div class="col-md-4 p-2 d-flex justify-content-center align-items-center text-center">
                    <a class="btn btn-success btn-tramitar" data-toggle="modal" data-target="#finalizar">Finalizar documento</a>
                </div>
                <div class="col-md-4 p-2 d-flex justify-content-center align-items-center text-center">
                    <a class="btn btn-primary btn-tramitar" data-toggle="modal" data-target="#aprovar">Aprovar documento</a>
                </div>
                <div class="col-md-4 p-2 d-flex justify-content-center align-items-center text-center">
                    <a class="btn btn-danger btn-tramitar" data-toggle="modal" data-target="#reprovar">Reprovar documento</a>
                </div>
            @else
                <div class="col-md-6 p-2 d-flex justify-content-center align-items-center text-center">
                    <a class="btn btn-success btn-tramitar" data-toggle="modal" data-target="#finalizar">Finalizar documento</a>
                </div>
                <div class="col-md-6 p-2 d-flex justify-content-center align-items-center text-center">
                    <a class="btn btn-danger btn-tramitar" data-toggle="modal" data-target="#reprovar">Reprovar documento</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="finalizar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('documento.finalizar', $documentoEdit->id) }}"
                method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="modal-header btn-success">
                    Finalizar documento
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <p class="mb-2" style="color: black">O documento será finalizado e não ficará mais ativo para alterações.</p>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Parecer</label>
                            <input type="text" class="form-control" name="parecer">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label w-100">Anexo</label>
                            <input type="file" name="anexo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Cancelar
                    </button>
                    <button type="submit" class="button_submit btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($aptoAprovar)

    <div class="modal fade" id="aprovar" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('documento.aprovar', [$documentoEdit->id, $documentoEdit->id_tipo_workflow]) }}"
                    method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="modal-header btn-primary">
                        Aprovar documento
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Próximo departamento na tramitação do documento</label>
                                <select class="form-control select2" name="id_departamento" id="id_departamento" required>
                                    @foreach ($departamentoTramitacao as $dep)
                                        <option value="{{ $dep->id_departamento }}">{{ $dep->departamento->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Parecer</label>
                                <input type="text" class="form-control" name="parecer" >
                            </div>
                            <div class="col-md-12">
                                <label class="form-label w-100">Anexo</label>
                                <input type="file" name="anexo">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Cancelar
                        </button>
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endif

<div class="modal fade" id="reprovar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('documento.reprovar', $documentoEdit->id) }}"
                method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="modal-header btn-danger">
                    Reprovar documento
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            @if ($depAnterior)
                                <label class="form-label">Departamento anterior na tramitação do documento</label>
                                <input type="text" class="form-control" value="{{ $depAnterior->departamento->descricao }}" readonly>
                            @else
                                <p class="mb-2" style="color: black">Não há departamento anterior na tramitação! O documento será reprovado e retornará ao autor.</p>
                            @endif
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Parecer</label>
                            <input type="text" class="form-control" name="parecer">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label w-100">Anexo</label>
                            <input type="file" name="anexo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Cancelar
                    </button>
                    <button type="submit" class="button_submit btn btn-danger">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
