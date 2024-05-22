@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Alteração de Pleito Eleitoral</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('processo_legislativo.legislatura.update', $legislatura->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Início do mandato</label>
                            <input type="text" class="ano form-control @error('inicio_mandato') is-invalid @enderror" name="inicio_mandato" value="{{ $legislatura->inicio_mandato }}" placeholder="somente ano(XXXX)">
                            @error('inicio_mandato')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Fim do mandato</label>
                            <input type="text" class="ano form-control @error('fim_mandato') is-invalid @enderror" name="fim_mandato" value="{{ $legislatura->fim_mandato }}" placeholder="somente ano(XXXX)">
                            @error('fim_mandato')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                            <a href="{{ route('processo_legislativo.legislatura.index') }}" class="btn btn-light m-1">Voltar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

