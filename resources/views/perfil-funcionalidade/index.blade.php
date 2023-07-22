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


<div class="card" style="background-color:white">

    <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Perfil e Funcionalidade</strong>
        </h2>
    </div>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="heading">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                    Cadastro de Perfil
                </button>
                </h5>
            </div>
            <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                <div class="card-body">
                    <form action="{{ route('perfil.store') }}" id="form-create-perfil" method="POST" class="form_prevent_multiple_submits">
                        @csrf
                        @method('POST')

                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">*Perfil</label>
                                    <input type="text" name="descricao" class="form-control" placeholder="Qual o nome do perfil?" value="{{ old('descricao') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">*Abrangência</label>
                                    <select name="id_abrangencia" class="form-control select2">
                                        <option value="" selected disabled>--Selecione--</option>
                                        @foreach ($abrangencias as $a)
                                            <option value="{{ $a->id }}">{{ $a->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">*Tipo de Perfil</label>
                                    <select name="id_tipo_perfil" class="form-control select2">
                                        <option value="" selected disabled>--Selecione--</option>
                                        @foreach ($tipo_perfis as $tp)
                                            <option value="{{ $tp->id }}">{{ $tp->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="accordion3">
        <div class="card">
            <div class="card-header" id="headingThree">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Vincular Perfil à Funcionalidade
                </button>
                </h5>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion3">
                <div class="card-body">
                    <form action="{{ route('perfil_funcionalidade.store') }}" id="form-create-funcionalidade" method="POST" class="form_prevent_multiple_submits">
                        @csrf
                        @method('POST')

                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="form-label">*Perfil</label>
                                    <select name="id_perfil" class="form-control select2">
                                        <option value="" selected disabled>--Selecione--</option>
                                        @foreach ($perfis as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->descricao }} -
                                                {{ $p->id_abrangencia != null ? $p->abrangencia->descricao : 'abrangência não informada' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="form-label">*Funcionalidade</label>
                                    <select name="id_funcionalidade[]" id="id_funcionalidade" class="form-control" multiple>
                                        @foreach ($funcionalidades as $f)
                                            <option value="{{ $f->id }}">
                                                {{ $f->entidade->descricao }} -
                                                {{ $f->id_tipo_funcionalidade != null ? $f->tipo_funcionalidade->descricao : 'tipo de funcionalidade não informada' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
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
                        Listagem de Perfis
                    </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion2">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Perfil</th>
                                    <th scope="col">Tipo de Perfil</th>
                                    <th scope="col">Cadastrado por</th>
                                    <th scope="col">Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pfs as $pf)
                                    <tr>
                                        <td>
                                            {{ $pf->descricao != null ? $pf->descricao : 'não informado' }} -
                                            {{ $pf->id_abrangencia != null ? $pf->abrangencia->descricao : 'não informado' }}
                                        </td>
                                        <td>{{ $pf->tipo_perfil->descricao }}</td>
                                        {{-- <td>
                                            @if (Count($pf->funcionalidades_ativas) != 0)
                                                <ol>
                                                    @foreach ($pf->funcionalidades_ativas as $f)
                                                        <li>
                                                            {{ $f->funcionalidade->id_entidade != null ? $f->funcionalidade->entidade->descricao : 'não informado' }} -
                                                            {{ $f->funcionalidade->id_tipo_funcionalidade != null ? $f->funcionalidade->tipo_funcionalidade->descricao : 'não informado' }}
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                Sem funcionalidades
                                            @endif
                                        </td> --}}
                                        <td>
                                            <strong>{{ $pf->cadastradoPorUsuario != null ? $pf->cad_usuario->pessoa->nomeCompleto : 'cadastrado pelo sistema' }}</strong>
                                            em <strong>{{ $pf->created_at != null ? $pf->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                        </td>
                                        {{-- <td>
                                            <a href="{{ route('perfil_funcionalidade.edit', $pf->id) }}"
                                            class="btn btn-warning">Alterar</a>
                                        </td> --}}
                                        <td>
                                            <a href="{{ route('perfil_funcionalidade.edit', $pf->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
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
<script src="{{asset('js/jquery.validate.js')}}"></script>

<script>

    $("#form-create-perfil").validate({
        rules : {
            descricao:{
                required:true
            },
            id_abrangencia:{
                required:true
            },
            id_tipo_perfil:{
                required:true
            }
        },
        messages:{
            descricao:{
                required:"Campo obrigatório"
            },
            id_abrangencia:{
                required:"Campo obrigatório"
            },
            id_tipo_perfil:{
                required:"Campo obrigatório"
            }
        }
    });

    $("#form-create-funcionalidade").validate({
        rules : {
            id_perfil:{
                required:true
            },
            "id_funcionalidade[]":{
                required:true
            }
        },
        messages:{
            id_perfil:{
                required:"Campo obrigatório"
            },
            "id_funcionalidade[]":{
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

        $('#id_funcionalidade').select2({
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                }
            },
            closeOnSelect: false,
            width: '100%',
            dropdownCssClass: "bigdrop"
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
