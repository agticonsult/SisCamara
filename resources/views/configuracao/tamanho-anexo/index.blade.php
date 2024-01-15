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
@include('errors.errors')


<h1 class="h3 mb-3">Tamanho dos Anexos</h1>
<div class="card" style="background-color:white">
    <div id="accordion2">
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Listagem
                    </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion2">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Tamanho do Arquivo</th>
                                    @if (
                                            Auth::user()->temPermissao('Filesize', 'Listagem') == 1   // mudar a permissão para usuario
                                        )
                                    <th scope="col">Cadastrado por</th>
                                    @endif
                                    <th scope="col">Editar Tamanho do Arquivo</th>
                                    <th scope="col">salvar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $files)
                                    <tr>
                                        <td>
                                            {{ $files->tipo_filesize->descricao != null ? $files->tipo_filesize->descricao : 'sem descrição do arquivo' }}
                                        </td>
                                        <td>
                                            {{ $files->mb != null ? $files->mb : 'tamanho do arquivo não informado' }}
                                            {{ $files->mb != null ? "mb" : '' }}
                                        </td>

                                        @if (
                                            Auth::user()->temPermissao('Filesize', 'Listagem') == 1     // mudar a permissão para usuario
                                        )
                                        <td>
                                            <strong>{{ $files->cadastradoPorUsuario != null ? $files->cad_usuario->pessoa->nome : 'cadastrado pelo sistema' }}</strong>
                                            em <strong>{{ $files->created_at != null ? $files->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                        </td>
                                        @endif
                                        {{-- <td>
                                            <a href="{{ route('configuracao.finalidade_grupo.edit', $finalidade->id) }}"
                                            class="btn btn-warning">Alterar</a>
                                        </td> --}}
                                        <form action="{{ route('configuracao.tamanho_anexo.update') }}" id="form-create-edit" method="POST" class="form_prevent_multiple_submits">
                                            @csrf
                                            @method('POST')
                                            <td>
                                                <input class="form-control" type="text" name="file_id" id="file_id"  value="{{ $files->id }}" hidden>
                                                <input type="text" name="file_mb" id="file_mb" class="valor form-control" value="{{ $files->mb }}" required>
                                                {{-- <input type="number" name="file_mb" id="file_mb" min='0' value="{{ $files->mb }}"> --}}
                                                {{-- <input class="form-control" type="text" name="file_mb" id="file_mb"  value="{{ $files->mb }}" hidden> --}}
                                            </td>
                                            <td>
                                                <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>

    $('.valor').mask('0000');

    $(document).ready(function() {

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
