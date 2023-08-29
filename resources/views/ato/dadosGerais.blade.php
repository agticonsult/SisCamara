<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card" style="background-color:white">

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('ato.dados_gerais.update', $ato->id) }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Classificação do Ato</label>
                        <select name="id_classificacao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($classificacaos as $classificacao)
                                <option value="{{ $classificacao->id }}" {{ $classificacao->id == $ato->id_classificacao ? 'selected' : '' }}>{{ $classificacao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Ano</label>
                        <input type="text" class="form-control" name="ano" id="ano" value="{{ $ato->ano }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Número</label>
                        <input type="text" class="form-control" name="numero" value="{{ $ato->numero }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Grupo</label>
                        <select name="id_grupo" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}" {{ $grupo->id == $ato->id_grupo ? 'selected' : '' }}>{{ $grupo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Tipo de Ato</label>
                        <select name="id_tipo_ato" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_atos as $tipo_ato)
                                <option value="{{ $tipo_ato->id }}" {{ $tipo_ato->id == $ato->id_tipo_ato ? 'selected' : '' }}>{{ $tipo_ato->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Assunto</label>
                        <select name="id_assunto" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($assuntos as $assunto)
                                <option value="{{ $assunto->id }}" {{ $assunto->id == $ato->id_assunto ? 'selected' : '' }}>{{ $assunto->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Órgão que editou o ato</label>
                        <select name="id_orgao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($orgaos as $orgao)
                                <option value="{{ $orgao->id }}" {{ $orgao->id == $ato->id_orgao ? 'selected' : '' }}>{{ $orgao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Forma de Publicação</label>
                        <select name="id_forma_publicacao" class="select2 form-control">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($forma_publicacaos as $forma_publicacao)
                                <option value="{{ $forma_publicacao->id }}" {{ $forma_publicacao->id == $ato->id_forma_publicacao ? 'selected' : '' }}>{{ $forma_publicacao->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Data de Publicação</label>
                        <input type="date" class="form-control" name="data_publicacao" value="{{ $ato->data_publicacao }}">
                    </div>
                </div>
                <div class="form-check col-md-6">
                    <input type="checkbox" class="form-check-input" id="altera_dispositivo" name="altera_dispositivo" {{ $ato->altera_dispositivo == 1 ? 'checked' : '' }}>
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
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
    $('#ano').mask('0000');

    $("#form").validate({
        rules : {
            // Dados Gerais
            id_classificacao:{
                required:true
            },
            ano:{
                required:true
            },
            numero:{
                required:true
            },
            id_grupo:{
                required:true
            },
            id_tipo_ato:{
                required:true
            },
            id_assunto:{
                required:true
            },
            id_orgao:{
                required:true
            },
        },
        messages:{
            // Dados Gerais
            id_classificacao:{
                required:"Campo obrigatório"
            },
            ano:{
                required:"Campo obrigatório"
            },
            numero:{
                required:"Campo obrigatório"
            },
            id_grupo:{
                required:"Campo obrigatório"
            },
            id_tipo_ato:{
                required:"Campo obrigatório"
            },
            id_assunto:{
                required:"Campo obrigatório"
            },
            id_orgao:{
                required:"Campo obrigatório"
            },
        }
    });

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
