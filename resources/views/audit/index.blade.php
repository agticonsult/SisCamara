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


<h1 class="h3 mb-3">Auditoria</h1>
<div class="card" style="padding: 3rem; background-color:white">

    <div id="accordion3">
        <div class="card">
            <div class="card-header" id="headingThree">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Filtros
                </button>
                </h5>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion3">
                <div class="card-body">
                    <form action="{{ route('auditoria.buscar') }}" method="post" id="form-buscar">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="data">Data</label>
                                <input type="date" name="data" class="dataFormat form-control" min="1899-01-01" max="2000-13-13">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="user_id">Usuário</label>
                                <select name="user_id" class="form-control select2">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->pessoa->nome }} - {{ $u->email != null ? $u->email : 'email não cadastrado' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="event">Evento</label>
                                <select name="event" class="form-control select2">
                                    <option value="" selected disabled>--Selecione--</option>
                                    <option value="created">Cadastro</option>
                                    <option value="updated">Atualização</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-primary float-right"><i class="fas fa-search-location"></i>&nbspPesquisar</button>
                            </div>
                        </div>
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
                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Evento</th>
                                    <th scope="col">Tabela alterada</th>
                                    <th scope="col">ID alterado</th>
                                    <th scope="col">IP</th>
                                    {{-- <th scope="col">Valores Antigos</th>
                                    <th scope="col">Valores Novos</th> --}}
                                    <th scope="col">Alterações</th>
                                    <th scope="col">Realizadas por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($audits as $audit)
                                    <tr>
                                        <td>{{ $audit->event != null ? $audit->event : '-' }}</td>
                                        <td>{{ $audit->auditable_type != null ? $audit->auditable_type : '-' }}</td>
                                        <td>{{ $audit->auditable_id != null ? $audit->auditable_id : '-' }}</td>
                                        <td>{{ $audit->ip_address != null ? $audit->ip_address : '-' }}</td>
                                        <td>{{ $audit->getModified(true) }}</td>
                                        {{-- <td >{{ json_encode($audit->old_values) }}</td>
                                        <td >{{ json_encode($audit->new_values) }}</td> --}}
                                        {{-- <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td> --}}
                                        <td>
                                            <strong>{{ $audit->user_id != null ? $audit->usuario->pessoa->nome : '-' }}</strong>
                                            em <strong>{{ $audit->created_at->format('d/m/Y H:i:s') }}</strong>
                                        </td>
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

    $('.cpf').mask('000.000.000-00');

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
    dd = '0' + dd;
    }

    if (mm < 10) {
    mm = '0' + mm;
    }

    today = yyyy + '-' + mm + '-' + dd;
    $('.dataFormat').attr('max', today);

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
