@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Cadastro de Repartição</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('reparticao.store') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Descrição</label>
                            <input type="text" class="form-control @error('descricao') is-invalid @enderror" name="descricao" placeholder="Descrição repartição" value="{{ old('descricao') }}">
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Tipo de Repartição</label>
                            <select name="id_tipo_reparticao" class="select2 form-control @error('id_tipo_reparticao') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($tipo_reparticaos as $tipo_reparticao)
                                    <option value="{{ $tipo_reparticao->id }}" {{ old('id_tipo_reparticao') == $tipo_reparticao->id ? 'selected' : '' }}>{{ $tipo_reparticao->descricao }}</option>
                                @endforeach
                            </select>
                            @error('id_tipo_reparticao')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            <a href="{{ route('reparticao.index') }}" class="btn btn-light">Voltar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    }
                },
                closeOnSelect: true,
                width: '100%',
            });

        });

    </script>
@endsection
