<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card" style="padding: 3rem; background-color:white">

    <div class="modal fade" id="ajaxModel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('ato.anexos.destroy', $ato->id) }}" id="form-inativar-anexo" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="modal-header btn-danger">
                        <h5 class="modal-title text-center" id="exampleModalLabel">
                            <strong>Excluir anexo</strong>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="anexo_id" id="anexo_id" hidden>
                        <div class="form-group">
                            <label for="anexo_nome">Anexo</label>
                            <input type="text" class="form-control" name="anexo_nome" id="anexo_nome" value="{{ old('anexo_nome') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="motivo">Motivo</label>
                            <input type="text" class="form-control" name="motivo" id="motivo" value="{{ old('motivo') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Cancelar
                        </button>
                        <button type="submit" class="button_submit btn btn-danger">Excluir anexo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="accordion2">
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Anexar arquivo ao ato
                </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion2">
                <div class="card-body">
                    <div class="col-md-12">
                        <form action="{{ route('ato.anexos.store', $ato->id) }}" class="form_prevent_multiple_submits" id="form-anexo" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <div class="col-md-12">
                                <br> Observações
                                <ul>
                                    <li>Tamanho máximo do anexo: {{ $filesize->mb }}MB</li>
                                </ul>
                                Extensões permitidas
                                <ul>
                                    <li>Documento (txt,pdf,xls,xlsx,doc,docx,odt)</li>
                                    <li>Imagem (jpg,jpeg,png)</li>
                                    <li>Áudio (mp3)</li>
                                    <li>Vídeo (mp4, mkv)</li>
                                </ul>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="anexo">Arquivo</label>
                                    <input type="file" name="anexo[]" id="anexo" class="form-control-file" multiple>
                                </div>
                            </div>

                            <br>
                            <div class="col-md-12">
                                <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><hr><br>

    <div id="accordion4">
        <div class="card">
            <div class="card-header" id="headingFour">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Arquivos anexados ao ato
                </button>
                </h5>
            </div>
            <div id="collapseFour" class="collapse show" aria-labelledby="headingFour" data-parent="#accordion4">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="tabela table table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nome Original</th>
                                            <th scope="col">Cadastrado por</th>
                                            <th scope="col">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ato->anexos_ativos() as $anexo)
                                            <tr>
                                                <td>
                                                    {{-- <a href="{{ route('gerenciamento.processo.anexo.get', ['id_processo' => $ato->id, 'id_anexo' => $anexo->id]) }}"> --}}
                                                    <a href="">
                                                        {{ $anexo->nome_original != "" && $anexo->nome_original != null ? $anexo->nome_original : '-' }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <strong>{{ $anexo->cadastradoPorUsuario != null ? $anexo->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                                    em <strong>{{ $anexo->created_at != null ? $anexo->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                                </td>
                                                @switch($anexo->ativo)
                                                    @case('1')
                                                        <td>
                                                            <button type="button" class="inativar-anexo btn btn-success" name="{{ $anexo->id }}" id="{{ $anexo->nome_original }}">
                                                                Ativo
                                                            </button>
                                                        </td>
                                                        @break

                                                    @default
                                                        <td>
                                                            <button type="button" class="btn btn-info">
                                                                Desativado
                                                                por <strong>{{ $anexo->inativadoPorUsuario != null ? $anexo->inativadoPor->pessoa->nomeCompleto : 'não informado' }}</strong>
                                                                em <strong>{{ date('d/m/Y H:i:s', strtotime($anexo->dataInativado)) }}</strong>
                                                            </button>
                                                        </td>
                                                        @break
                                                @endswitch
                                                {{-- <td>
                                                    <button type="button" class="excluir_anexo btn btn-danger" id="{{ $anexo->id }}" name="{{ $anexo->nome_original }}">
                                                        <i class="fas fa-times"></i>
                                                        Excluir
                                                    </button>
                                                </td> --}}
                                            </tr>
                                            {{-- <td><a href="{{ route('atendimento_social.encaminhamento.formulario.edit', ['id_prontuario' => $prontuario->id, 'id_form' => $fe->id])}}" class="btn btn-warning">Alterar</a></td> --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>

<script>

    $("#form-anexo").validate({
        rules : {
            "arquivo[]":{
                required:true
            },
            id_tipo_anexo:{
                required:true
            }
        },
        messages:{
            "arquivo[]":{
                required:"Campo obrigatório",
            },
            id_tipo_anexo:{
                required:"Campo obrigatório"
            }
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

        $('.inativar-anexo').click(function () {
            var nome = this.id;
            var id = this.name;
            $('#anexo_nome').val(nome);
            $('#anexo_id').val(id);
            $('#ajaxModel2').modal('show');
        });

        $('.tabela').dataTable({
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
