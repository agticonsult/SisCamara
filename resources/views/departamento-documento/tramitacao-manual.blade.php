<div class="card-body">
    <div class="col-md-12">
        <div class="row">
            <div class="form-group col-md-4">
                <label class="form-label">*Status</label>
                <select name="id_status" id="id_status" class="form-control @error('id_status') is-invalid @enderror">
                    <option value="" selected disabled>--Selecione--</option>
                    @foreach ($statusDepDocs as $status)
                        <option value="{{ $status->id }}">{{ $status->descricao }}</option>
                    @endforeach
                </select>
                @error('id_status')
                    <div class="invalid-feedback">{{ $message }}</div><br>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label class="form-label">Parecer</label>
                <input type="text" class="form-control" value="{{ $historicoMovimentacao->parecer }}">
            </div>
            <div class="form-group col-md-4">
                <label class="form-label">Departamento</label>
                <select name="id_departamento" class="form-control @error('id_departamento') is-invalid @enderror select2">
                    <option value="" selected disabled>-- Selecione --</option>
                    @foreach ($departamentoTramitacao as $dep)
                        <option value="{{ $dep->id_departamento }}">{{ $dep->departamento->descricao }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
