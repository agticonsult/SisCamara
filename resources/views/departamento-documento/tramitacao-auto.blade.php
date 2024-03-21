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
            <div class="col-md-6 p-2 d-flex justify-content-center align-items-center text-center">
                @if ($aptoFinalizar)
                    <a class="btn btn-success btn-tramitar" data-toggle="modal" data-target="#finalizar">Finalizar documento</a>
                @else
                    <a class="btn btn-primary btn-tramitar" data-toggle="modal" data-target="#aprovar">Aprovar documento</a>
                @endif
            </div>
            <div class="col-md-6 p-2 d-flex justify-content-center align-items-center text-center">
                <a class="btn btn-danger btn-tramitar" data-toggle="modal" data-target="#reprovar">Reprovar documento</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="finalizar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="modal-header btn-success">
                    Finalizar documento
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Parecer</label>
                            <input type="text" class="form-control @error('parecer') is-invalid @enderror" name="parecer" >
                            @error('parecer')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
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

<div class="modal fade" id="aprovar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="modal-header btn-primary">
                    Aprovar documento
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Próximo departamento na tramitação do documento</label>
                            <input type="text" class="form-control" value="{{ $proximoDep->departamento->descricao }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Parecer</label>
                            <input type="text" class="form-control @error('parecer') is-invalid @enderror" name="parecer" >
                            @error('parecer')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
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

<div class="modal fade" id="reprovar" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" class="form_prevent_multiple_submits">
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
                                <p class="mb-2" style="color: black">Não há departamento anterior na tramitação! O documento será reprovado e não ficará mais ativo para alterações.</p>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Parecer</label>
                            <input type="text" class="form-control @error('parecer') is-invalid @enderror" name="parecer" >
                            @error('parecer')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
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


