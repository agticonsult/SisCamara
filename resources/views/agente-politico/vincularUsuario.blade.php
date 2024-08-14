@extends('layout.main')

@section('content')

    <style>
        .error{
            color:red
        }
        input[type='file'] {
            display: none;
        }
        /* .max-width {
            max-width: 500px;
            width: 100%;
        } */
        #imgPhoto {
            margin-top: 10%;
            /* width: 100%;
            height: 100%; */
            /* padding:10px; */
            background-color: #eee;
            border: 5px solid #ccc;
            border-radius: 50%;
            cursor: pointer;
            transition: background .3s;
        }
        #imgPhoto:hover{
            background-color: rgb(180, 180, 180);
            border: 5px solid #111;
        }
    </style>
    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Agentes Políticos > </span>Vincular Agente Político</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('agente_politico.storeVincular') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Pleito Eleitoral</label>
                            <select name="id_pleito_eleitoral" id="id_pleito_eleitoral" class="select2 form-control @error('id_pleito_eleitoral') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($pleito_eleitorals as $pleito_eleitoral)
                                    <option value="{{ $pleito_eleitoral->id }}">
                                        Primeiro Turno: <strong>{{ date('d/m/Y', strtotime($pleito_eleitoral->dataPrimeiroTurno)) }}</strong> -
                                        Segundo Turno: <strong>{{ date('d/m/Y', strtotime($pleito_eleitoral->dataPrimeiroTurno)) }}</strong>
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pleito_eleitoral')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Cargo Eletivo</label>
                            <select name="id_cargo_eletivo" id="id_cargo_eletivo" class="select2 form-control @error('id_cargo_eletivo') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                            </select>
                            @error('id_cargo_eletivo')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Data início mandato</label>
                            <input type="date" name="dataInicioMandato" class="form-control @error('dataInicioMandato') is-invalid @enderror" value="{{ old('dataInicioMandato') }}">
                            @error('dataInicioMandato')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Data fim mandato</label>
                            <input type="date" name="dataFimMandato" class="form-control @error('dataFimMandato') is-invalid @enderror" value="{{ old('dataFimMandato') }}">
                            @error('dataFimMandato')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="id_usuario">*Usuário</label>
                            <select name="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror select2">
                                <option value="" selected disabled>-- Selecione --</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->pessoa->nome }}</option>
                                @endforeach
                            </select>
                            @error('id_usuario')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                            <a href="{{ route('agente_politico.index') }}" class="btn btn-light m-1">Voltar</a>
                        </div>
                        <br>
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

            $('#id_pleito_eleitoral').on('change', function() {
                var b = true;

                var id_pleito_eleitoral = $('#id_pleito_eleitoral').select2("val");
                $.ajax({
                    url: "{{ route('processo_legislativo.pleito_eleitoral.get', '') }}"  + "/" + id_pleito_eleitoral,
                    type: "GET",
                    dataType: 'json',
                    success: function (resposta) {
                        if (resposta.data){
                            $('select[name=id_cargo_eletivo]').empty();
                            resposta.data.forEach(cargo_eletivo => {
                                if (b){
                                    $('select[name=id_cargo_eletivo]').append('<option value="" selected disabled>--Selecione--</option>');
                                }
                                b = false;
                                $('select[name=id_cargo_eletivo]').append('<option value=' + cargo_eletivo.id +
                                    '>' + cargo_eletivo.descricao + '</option>');
                            });
                        }
                        else{
                            if (resposta.erro){
                                alert('Erro! Contate o administrador do sistema.');
                            }
                        }
                    },
                    error: function (resposta) {
                        alert('Erro! Contate o administrador do sistema.');
                    }
                });
            });
        });

    </script>
@endsection
