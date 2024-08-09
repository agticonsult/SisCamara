
<div class="card" style="background-color:white">
    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('ato.dados_gerais.update', $ato->id) }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Classificação do Ato</label>
                        <select name="id_classificacao" class="select2 form-control @error('id_classificacao') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($classificacaos as $classificacao)
                                <option value="{{ $classificacao->id }}" {{ $classificacao->id == $ato->id_classificacao ? 'selected' : '' }}>{{ $classificacao->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_classificacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Ano</label>
                        <input type="text" class="form-control @error('ano') is-invalid @enderror" name="ano" id="ano" value="{{ $ato->ano }}">
                        @error('ano')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Número</label>
                        <input type="text" class="form-control @error('numero') is-invalid @enderror" name="numero" value="{{ $ato->numero }}">
                        @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">Data de Publicação</label>
                        <input type="date" class="form-control @error('data_publicacao') is-invalid @enderror" name="data_publicacao" value="{{ $ato->data_publicacao }}">
                        @error('data_publicacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Tipo de Ato</label>
                        <select name="id_tipo_ato" class="select2 form-control @error('id_tipo_ato') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($tipo_atos as $tipo_ato)
                                <option value="{{ $tipo_ato->id }}" {{ $tipo_ato->id == $ato->id_tipo_ato ? 'selected' : '' }}>{{ $tipo_ato->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_tipo_ato')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">*Assunto</label>
                        <select name="id_assunto" class="select2 form-control @error('id_assunto') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($assuntos as $assunto)
                                <option value="{{ $assunto->id }}" {{ $assunto->id == $ato->id_assunto ? 'selected' : '' }}>{{ $assunto->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_assunto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="form-label">*Órgão que editou o ato</label>
                        <select name="id_orgao" class="select2 form-control @error('id_orgao') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($orgaos as $orgao)
                                <option value="{{ $orgao->id }}" {{ $orgao->id == $ato->id_orgao ? 'selected' : '' }}>{{ $orgao->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_orgao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Forma de Publicação</label>
                        <select name="id_forma_publicacao" class="select2 form-control @error('id_forma_publicacao') is-invalid @enderror">
                            <option value="" selected disabled>--Selecione--</option>
                            @foreach ($forma_publicacaos as $forma_publicacao)
                                <option value="{{ $forma_publicacao->id }}" {{ $forma_publicacao->id == $ato->id_forma_publicacao ? 'selected' : '' }}>{{ $forma_publicacao->descricao }}</option>
                            @endforeach
                        </select>
                        @error('id_forma_publicacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
