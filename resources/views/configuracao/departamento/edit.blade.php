@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- script referente ao mapa --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14="
crossorigin=""/>
<script src='https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.js'></script>
<link href='https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.css' rel='stylesheet' />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')

<h1 class="h3 mb-3">Alteração do Departamento</h1>
<div class="card" style="background-color:white">

    <div class="card-body">
        <div class="col-md-12">
            <form action="#" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Nome</label>
                            <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao" value="{{ $departamento->descricao != null ? $departamento->descricao : old('descricao') }}" placeholder="Nome do departamento">
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="id_coordenador">Coordenador</label>
                            <select name="id_coordenador" class="form-control @error('id_coordenador') is-invalid @enderror select2">
                                <option value="" selected disabled>-- Selecione --</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('id_coordenador') == $usuario->id ? 'selected' : '' }}>{{ $usuario->pessoa->nome }}</option>
                                @endforeach
                            </select>
                            @error('id_coordenador')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label">Usuário</label>
                            <select name="id_user[]" class="form-control @error('id_user') is-invalid @enderror select2" multiple>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('id_user') == $usuario->id ? 'selected' : '' }}>{{ $usuario->pessoa->nome }}</option>
                                @endforeach
                            </select>
                            {{-- <select class="select_multiple form-control @error('id_perfil') is-invalid @enderror" name="id_perfil[]" multiple>
                                @foreach ($usuarios as $usuario)
                                    @php
                                        $temUsuario = 0;
                                        if ($departamento->id_perfil == $usuario->id){
                                            $temUsuario = 1;
                                        }
                                    @endphp
                                    @if ($temUsuario == 1)
                                        <option value="{{ $usuario->id }}" selected>{{ $usuario->pessoa->nome }}</option>
                                    @else
                                        <option value="{{ $usuario->id }}">{{ $usuario->pessoa->nome }}</option>
                                    @endif
                                @endforeach
                            </select> --}}
                            @error('id_user')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            <a href="{{ route('configuracao.departamento.index') }}" class="btn btn-light">Voltar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
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

        $('#datatables-reponsive').dataTable({
            "oLanguage": {
                "sLengthMenu": "Mostrar _MENU_ registros por página",
                "sZeroRecords": "Nenhum registro encontrado",
                "sInfo": "Mostrando _START_ / _END_ de _TOTAL_ registro(s)",
                "sInfoEmpty": "Mostrando 0 / 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros)",
                "sSearch": "Pesquisar: ",
                "oPaginate": {
                    "sFirst": "Início",
                    "sPrevious": "Anterior",
                    "sNext": "Próximo",
                    "sLast": "Último"
                }
            },
        });
    });
</script>

@endsection
