@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')

<h1 class="h3 mb-3">Cadastro de Pleito Eleitoral</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('processo_legislativo.pleito_eleitoral.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Ano do Pleito Eleitoral</label>
                        <input type="text" class="ano form-control @error('ano_pleito') is-invalid @enderror" name="ano_pleito" value="{{ old('ano_pleito') }}">
                        @error('ano_pleito')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">Pleito Especial</label>
                        <select name="pleitoEspecial" class="form-control @error('pleitoEspecial') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                        @error('pleitoEspecial')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data do primeiro turno</label>
                        <input type="date" class="form-control @error('dataPrimeiroTurno') is-invalid @enderror" name="dataPrimeiroTurno" value="{{ old('dataPrimeiroTurno') }}">
                        @error('dataPrimeiroTurno')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Data do segundo turno</label>
                        <input type="date" class="form-control @error('dataSegundoTurno') is-invalid @enderror" name="dataSegundoTurno" value="{{ old('dataSegundoTurno') }}">
                        @error('dataSegundoTurno')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Legislatura</label>
                        <select name="id_legislatura" class="select2 form-control @error('id_legislatura') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($legislaturas as $legislatura)
                                <option value="{{ $legislatura->id }}" {{ old('id_legislatura') == $legislatura->id ? 'selected' : '' }}>
                                    Início: <strong>{{ $legislatura->inicio_mandato }}</strong> -
                                    Fim: <strong>{{ $legislatura->fim_mandato }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_legislatura')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Cargos eletivos</label>
                        <select name="id_cargo_eletivo[]" class="select2 form-control @error('id_cargo_eletivo') is-invalid @enderror" multiple>
                            @foreach ($cargo_eletivos as $cargo_eletivo)
                                <option value="{{ $cargo_eletivo->id }}">{{ $cargo_eletivo->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_cargo_eletivo')
                            <div class="invalid-feedback">{{ $message }}</div><br>
                        @enderror
                    </div>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                    <a href="{{ route('processo_legislativo.pleito_eleitoral.index') }}" class="btn btn-light m-1">Voltar</a>
                </div>
                <br>
            </form>
        </div>
    </div>

</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
    $('.ano').mask('0000');

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
