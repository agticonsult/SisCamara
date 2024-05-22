@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Alterar Gestão Administrativa</h1>

    <div class="card">
        <div class="card-body">
            <form action="#" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="col-md-12">
                    <h5>Aprovação de cadastro e recebimento do documento de Usuário Externo</h5>
                    <hr>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">*Departamento</label>
                            <select name="id_departamento" class="form-control select2 @error('id_departamento') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($departamentosArray as $dep)
                                    <option value="{{ $dep->id }}" {{ $alterarGestaoAdm->id_departamento ? 'selected' : '' }}>
                                        {{ $dep->descricao }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_departamento')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Aprovação de cadastro</label>
                            <select name="aprovacaoCadastro" class="form-control @error('aprovacaoCadastro') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                            @error('aprovacaoCadastro')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Recebimento de documento</label>
                            <select name="recebimentoDocumento" class="form-control @error('recebimentoDocumento') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                            @error('recebimentoDocumento')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

@endsection
