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

<h1 class="h3 mb-3">Legislaturas</h1>
<div class="card" style="background-color:white">
    <div id="accordion">
        <div class="card-header" id="heading">
            <h5 class="mb-0">
            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                Cadastro
            </button>
            </h5>
        </div>
        <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
            <div class="card-body">
                <div class="col-md-12">
                    <form action="{{ route('processo_legislativo.legislatura.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                        @csrf
                        @method('POST')

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Início do mandato</label>
                                <input type="text" class="ano form-control @error('inicio_mandato') is-invalid @enderror" name="inicio_mandato" placeholder="somente ano(XXXX)" value="{{ old('inicio_mandato') }}">
                                @error('inicio_mandato')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">*Fim do mandato</label>
                                <input type="text" class="ano form-control @error('fim_mandato') is-invalid @enderror" name="fim_mandato" placeholder="somente ano(XXXX)" value="{{ old('fim_mandato') }}">
                                @error('fim_mandato')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
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
    </div>
</div>

<div class="card" style="background-color:white">
    <div id="accordion">
        <div class="card-header" id="heading">
            <h5 class="mb-0">
            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                Cadastro
            </button>
            </h5>
        </div>
        <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
            <div class="card-body">
                <div class="col-md-12">
                    <form action="{{ route('processo_legislativo.legislatura.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                        @csrf
                        @method('POST')

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Início do mandato</label>
                                <input type="text" class="ano form-control @error('inicio_mandato') is-invalid @enderror" name="inicio_mandato" placeholder="somente ano(XXXX)" value="{{ old('inicio_mandato') }}">
                                @error('inicio_mandato')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">*Fim do mandato</label>
                                <input type="text" class="ano form-control @error('fim_mandato') is-invalid @enderror" name="fim_mandato" placeholder="somente ano(XXXX)" value="{{ old('fim_mandato') }}">
                                @error('fim_mandato')
                                    <div class="invalid-feedback">{{ $message }}</div><br>
                                @enderror
                            </div>
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
    </div>

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
                    @if (Count($legislaturas) == 0)
                        <div>
                            <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Mandato</th>
                                        <th scope="col">Cadastrado por</th>
                                        <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($legislaturas as $legislatura)
                                        <tr>
                                            <td>Início: <strong>{{ $legislatura->inicio_mandato }}</strong> - Fim: <strong>{{ $legislatura->fim_mandato }}</strong></td>
                                            <td>
                                                <strong>{{ $legislatura->cadastradoPorUsuario != null ? $legislatura->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                                em <strong>{{ $legislatura->created_at != null ? $legislatura->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('processo_legislativo.legislatura.edit', $legislatura->id) }}" class="btn btn-warning m-1"><i class="fas fa-pen"></i></a>
                                                <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $legislatura->id }}"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="exampleModalExcluir{{ $legislatura->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="POST" class="form_prevent_multiple_submits" action="{{ route('processo_legislativo.legislatura.destroy', $legislatura->id) }}">
                                                        @csrf
                                                        @method('POST')
                                                        <div class="modal-header btn-danger">
                                                            <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                                    Excluir Legislatura -
                                                                    <strong>
                                                                    Início:
                                                                    {{ $legislatura->inicio_mandato }}
                                                                    - Fim:
                                                                    {{ $legislatura->fim_mandato }}
                                                                    </strong>
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="motivo" class="form-label">Motivo</label>
                                                                <input type="text" class="form-control" name="motivo">
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
    $('.ano').mask('0000');

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
