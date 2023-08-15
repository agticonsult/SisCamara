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

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('ato.alterarLinha') }}" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="modal-header btn-success">
                        <h5 class="modal-title text-center" id="exampleModalLabel">
                            <strong>Alteração dos dados do dispositivo</strong>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- <div class="form-group col-md-6">
                                <label for="data">Data</label>
                                <input type="date" class="form-control" name="data" id="data" readonly>
                            </div> --}}
                            <input type="hidden" name="id_linha_ato" id="id_linha_ato">
                            <div class="form-group col-md-12">
                                <label for="id_ato_add">*Ato que contém a alteração</label>
                                <select name="id_ato_add" id="id_ato_add" class="form-control select2">
                                    <option value="" selected disabled>-- Selecione --</option>
                                    @foreach ($atos_relacionados as $atos_relacionado)
                                        <option value="{{ $atos_relacionado->id }}">
                                            @php
                                                setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                                                date_default_timezone_set('America/Sao_Paulo');
                                            @endphp
                                            {{ $atos_relacionado->id_tipo_ato != null ? $atos_relacionado->tipo_ato->descricao : 'Tipo de ato não informado' }}
                                            Nº {{ $atos_relacionado->numero != null ? $atos_relacionado->numero : 'não informado' }},
                                            de {{ strftime('%d de %B de %Y', strtotime($atos_relacionado->created_at)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label">*Nova linha</label>
                                <textarea name="corpo_texto" cols="10" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Cancelar
                        </button>
                        <button type="submit" class="button_submit btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if (Count($ato->todas_linhas_ativas()) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Texto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ato->linhas_inalteradas_ativas() as $linha)
                            <tr>
                                {{-- @if ($linha->alterado == 1)
                                    <td>
                                        <button class="btn btn-danger"><i class="fas fa-times"></i></button>
                                    </td>
                                    <td class="d-none"></td>
                                    <td colspan="2" style="text-decoration: line-through">{{ $linha->texto }}</td>
                                @else --}}
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch{{ $linha->id }}" name="{{ $linha->id }}">
                                            <label class="custom-control-label" for="customSwitch{{ $linha->id }}"></label>
                                            {{-- <label class="custom-control-label" for="customSwitch{{ $linha->id }}">Clique para selecionar</label> --}}
                                        </div>
                                    </td>
                                    <td>{{ $linha->texto }}</td>
                                {{-- @endif --}}
                            </tr>
                                {{-- <td>
                                    @php
                                        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                                        date_default_timezone_set('America/Sao_Paulo');
                                    @endphp
                                    {{ $ato->id_tipo_ato != null ? $ato->tipo_ato->descricao : 'Tipo de ato não informado' }}
                                    Nº {{ $ato->numero != null ? $ato->numero : 'não informado' }},
                                    de {{ strftime('%d de %B de %Y', strtotime($ato->created_at)) }}
                                </td>
                                <td>{{ $ato->titulo }}</td>
                                <td>
                                    <strong>{{ $ato->cadastradoPorUsuario != null ? $ato->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $ato->created_at != null ? $ato->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('ato.show', $ato->id) }}" class="btn btn-secondary m-1">Visualizar</a>
                                    <a href="{{ route('ato.edit', $ato->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $ato->id }}">Excluir</button>
                                </td> --}}

                            {{-- <div class="modal fade" id="exampleModalExcluir{{ $usuario->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('usuario.destroy', $usuario->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    <strong style="font-size: 1.2rem">Excluir <i>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</i></strong>
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="motivo" class="form-label">Motivo</label>
                                                    <input type="text" class="form-control" name="motivo" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                                                </button>
                                                <button type="submit" class="button_submit btn btn-danger">Excluir</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="exampleModalRecadastrar{{ $usuario->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelRecadastrar"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('usuario.restore', $usuario->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-primary">
                                                <h5 class="modal-title text-center" id="exampleModalLabelRecadastrar">
                                                    <strong style="font-size: 1.2rem">Recadastrar <i>{{ $usuario->pessoa->nomeCompleto != null ? $usuario->pessoa->nomeCompleto : 'não informado' }}</i></strong>
                                                </h5>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                                                </button>
                                                <button type="submit" class="button_submit btn btn-primary">Recadastrar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card-footer">
        <a href="{{ route('ato.create') }}" class="btn btn-primary">Cadastrar Ato</a>
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
