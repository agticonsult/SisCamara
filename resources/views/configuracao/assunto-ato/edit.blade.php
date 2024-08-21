@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Configuração > </span>Alteração Assunto do Ato</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('configuracao.assunto_ato.update', $assunto->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Nome</label>
                                <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao" value="{{ $assunto->descricao != null ? $assunto->descricao : old('descricao') }}">
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        <a href="{{ route('configuracao.assunto_ato.index') }}" class="btn btn-light">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
