<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')
@include('errors.errors')

<div class="card" style="background-color:white">

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('ato.updateDadosGerais') }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Ano</label>
                        <input type="text" class="form-control" name="ano">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Número</label>
                        <input type="text" class="form-control" name="numero">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Grupo</label>
                        <select name="id_grupo" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">*Tipo de Ato</label>
                        <select name="id_tipo_ato" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_atos as $tipo_ato)
                                <option value="{{ $tipo_ato->id }}">{{ $tipo_ato->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">*Assunto</label>
                        <select name="id_assunto" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($assuntos as $assunto)
                                <option value="{{ $assunto->id }}">{{ $assunto->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-check col-md-6">
                    <input type="checkbox" class="form-check-input" id="altera_dispositivo" name="altera_dispositivo">
                    <label class="form-check-label" for="altera_dispositivo">Este ato altera algum dispositivo legal</label>
                </div>

                <br>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                </div>
                <br>
            </form>
        </div>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>

    // $('#ajaxModel').modal('hide', function(){
    //     console.log("Close");
    // });

    $(document).ready(function() {

        var quantidade_checks = 0;
        var id_ultimo_clicado = null;

        $('.custom-control-input').on('change.bootstrapSwitch', function(e){
            if (id_ultimo_clicado != null && id_ultimo_clicado != ''){
                if (id_ultimo_clicado != this.id){
                    $('#' + id_ultimo_clicado).prop("checked", false);
                    $('#id_linha_ato').val(this.name);
                }
                else{
                    if (this.checked == false){
                        $('#id_linha_ato').val('');
                    }
                    else{
                        $('#id_linha_ato').val(this.name);
                    }
                }
            }
            else{
                $('#id_linha_ato').val(this.name);
            }

            id_ultimo_clicado = this.id;
            $('#ajaxModel').modal('show');
            // console.log(this);
            // console.log(this.name);
            // console.log($(this).attr("descricao"));
        });

        $("#ajaxModel").on('hide.bs.modal', function(){
            $('#' + id_ultimo_clicado).prop("checked", false);
            // alert('The modal is about to be hidden.');
        });

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
