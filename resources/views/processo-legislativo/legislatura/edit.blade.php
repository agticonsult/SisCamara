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

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary m-1">Salvar</button>
                    <a href="{{ route('processo_legislativo.legislatura.index') }}" class="btn btn-light m-1">Voltar</a>
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
