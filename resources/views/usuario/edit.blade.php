@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAUgxBPrGkKz6xNwW6Z1rJh26AqR8ct37A"></script>
<script src="{{ asset('js/gmaps.js') }}"></script>

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
            <strong>Alteração de Usuário</strong>
        </h2>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('usuario.desativaPerfil', $usuario->id) }}" id="form-atualizacao" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="modal-header btn-danger">
                        <h5 class="modal-title text-center" id="exampleModalLabel">
                            <strong>Desativar o perfil deste usuário?</strong>
                        </h5>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <input class="form-control" type="text" name="id_user_desativa" id="id_user_desativa" value="{{ $usuario->id }}" hidden>
                            <input class="form-control" type="text" name="permissao_id" id="permissao_id" hidden>
                            <div class="modal-body col-md-6">
                                <label for="usuario">Usuário</label>
                                <input class="form-control" type="text" name="usuario" value="{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : old('usuario') }}" readonly>
                            </div>
                            <div class="modal-body col-md-6">
                                <label for="permissao_descricao">Perfil</label>
                                <input class="form-control" type="text" name="permissao_descricao" id="permissao_descricao" readonly>
                            </div>
                            <div class="modal-body col-md-12">
                                <label for="motivo">Motivo</label>
                                <input class="form-control" type="text" name="motivo" value="{{ old('motivo') }}">
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">Cancelar
                            </button>
                            <button type="submit" class="button_submit btn btn-danger">Desativar</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header btn-info">
                    <h5 class="modal-title text-center" id="exampleModalLabel">
                        <strong>Funcionalidades do Perfil <span id="descricao_perfil"></span></strong>
                    </h5>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="modal-body col-md-12">
                            <ol id="funcionalidades">
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('usuario.update', $usuario->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <h5>Dados Pessoais</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Nome</label>
                                <input class="form-control" type="text" name="nomeCompleto" id="nomeCompleto" value="{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : old('nomeCompleto') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">*Email</label>
                                <input class="form-control" type="email" name="email" value="{{ $usuario->email != null ? $usuario->email : old('email') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">*CPF</label>
                                <input class="cpf form-control" type="text" name="cpf" id="cpf" value="{{ $usuario->cpf != null ? $usuario->cpf : old('cpf') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">*Data de Nascimento</label>
                                <input class="dataFormat form-control" type="date" min="1899-01-01" max="2000-13-13" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ $usuario->pessoa->dt_nascimento_fundacao != null ? $usuario->pessoa->dt_nascimento_fundacao: old('dt_nascimento_fundacao') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">*Perfil</label>
                                <select name="id_perfil[]" class="form-control select2" multiple>
                                    @foreach ($perfils as $perfil)
                                        @foreach ($usuario->permissoes_ativas as $permissao)
                                            @if ($permissao->id_perfil != $perfil->id)
                                                <option value="{{ $perfil->id }}">{{ $perfil->descricao }}</option>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                </div>
                <br>
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <hr><br>
            <h5>Listagem de Perfis</h5>
            <br>
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Usuário</th>
                            <th scope="col">CPF</th>
                            <th scope="col">Email</th>
                            <th scope="col">Perfil (clique no botão para ver as funcionalidades do perfil)</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Status <br>(para desativar este perfil deste usuário, clique no botão "Ativo")</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuario->permissoes as $permissao)
                            <tr>
                                <td>{{ $permissao->user->id_pessoa != null ? $permissao->user->pessoa->nomeCompleto : 'não informado' }}</td>
                                <td class="cpf">{{ $permissao->user->cpf != null ? $permissao->user->cpf : 'não informado' }}</td>
                                <td>{{ $permissao->user->email != null ? $permissao->user->email : 'não informado' }}</td>
                                <td>
                                    <button  type="button" class="funcionalidades btn btn-dark" id="{{ $permissao->id_perfil }}" name="{{ $permissao->perfil->descricao }}">
                                        {{ $permissao->perfil->descricao }}
                                    </button>
                                </td>
                                <td>
                                    <strong>{{ $permissao->cadastradoPorUsuario != null ? $permissao->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                    em <strong>{{ $permissao->created_at != null ? $permissao->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    @switch($permissao->ativo)
                                        @case(1)
                                            <button type="button" class="desativar btn btn-success" name="{{ $permissao->id }}" id="{{ $permissao->perfil->descricao }}">
                                                Ativo
                                            </button>
                                            @break
                                        @default
                                            <button type="button" class="btn btn-info">
                                                Desativado
                                                por <strong>{{ $permissao->inativadoPorUsuario != null ? $permissao->inativadoPor->pessoa->nomeCompleto : 'não informado' }}</strong>
                                                em <strong>{{ date('d/m/Y H:i:s', strtotime($permissao->dataInativado)) }}</strong>
                                            </button>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>

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

    $("#form").validate({
        rules : {
            nomeCompleto:{
                required:true
            },
            cpf:{
                required:true
            },
            dt_nascimento_fundacao:{
                required:true
            },
            email:{
                required:true
            },
            // lotacao:{
            //     required:true
            // }
        },
        messages:{
            nomeCompleto:{
                required:"Campo obrigatório"
            },
            cpf:{
                required:"Campo obrigatório"
            },
            dt_nascimento_fundacao:{
                required:"Campo obrigatório",
                min:"Data mínima: 01/01/1899",
                max:"Data máxima: data de hoje",
            },
            email:{
                required:"Campo obrigatório"
            },
            // lotacao:{
            //     required:"Campo obrigatório"
            // }
        }
    });

    $('.funcionalidades').on('click', function(){
        var id_perfil = this.id;
        var descricao_perfil = (this).name;

        $.ajax({
            url: "{{ route('perfil.funcionalidades', '') }}"  + "/" + id_perfil,
            dataType: 'json',
            success: function(resposta){
                $('#funcionalidades').empty();
                var lista = $('#funcionalidades');
                var funcionalidades = resposta.data.funcionalidades;
                if (funcionalidades.length != 0){
                    for (let i = 0; i < funcionalidades.length; i++) {
                        lista.append('<li>' + funcionalidades[i] + '</li>');
                    }
                }
                else{
                    lista.append('<li style="list-style-type: none;">Este Perfil não tem funcionalidades cadastradas!</li>');
                }
                $('#descricao_perfil').text(descricao_perfil);
                $('#ajaxModel2').modal('show');
            },
            error: function(resposta){
                alert(resposta.responseJSON.message);
            },
        });
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

        $('#tipo_perfil').on('change', function(){
            var valores = $('#tipo_perfil').val();

            switch (valores.length) {

                case 1:
                    selected = valores[0];
                    switch (selected) {
                        case '1':
                            $('#administrador').removeClass('d-none');
                            $('#funcionario').addClass('d-none');
                            $('#cliente').addClass('d-none');
                            break;
                        case '2':
                            $('#funcionario').removeClass('d-none');
                            $('#administrador').addClass('d-none');
                            $('#cliente').addClass('d-none');
                            break;
                        case '3':
                            $('#cliente').removeClass('d-none');
                            $('#administrador').addClass('d-none');
                            $('#funcionario').addClass('d-none');
                            break;

                        default:
                            $('#administrador').addClass('d-none');
                            $('#funcionario').addClass('d-none');
                            $('#cliente').addClass('d-none');
                            break;
                    }
                    break;

                case 2:
                    console.log(valores);
                    if ((valores[0] == 1 && valores[1] == 2) || (valores[0] == 2 && valores[1] == 1)){
                        $('#administrador').removeClass('d-none');
                        $('#funcionario').removeClass('d-none');
                        $('#cliente').addClass('d-none');
                    }
                    else{
                        if ((valores[0] == 1 && valores[1] == 3) || (valores[0] == 3 && valores[1] == 1)){
                            $('#administrador').removeClass('d-none');
                            $('#cliente').removeClass('d-none');
                            $('#funcionario').addClass('d-none');
                        }
                        else{
                            if ((valores[0] == 2 && valores[1] == 3) || (valores[0] == 3 && valores[1] == 2)){
                                $('#funcionario').removeClass('d-none');
                                $('#cliente').removeClass('d-none');
                                $('#administrador').addClass('d-none');
                            }
                        }
                    }
                    break;

                case 3:
                    console.log(valores);
                    $('#administrador').removeClass('d-none');
                    $('#funcionario').removeClass('d-none');
                    $('#cliente').removeClass('d-none');
                    break;

                default:
                    break;
            }
        });

        $('.desativar').click(function () {
            var permissao_id = this.name;
            var permissao_descricao = this.id;
            $('#permissao_id').val(permissao_id);
            $('#permissao_descricao').val(permissao_descricao);
            $('#ajaxModel').modal('show');
        });

        $('.adicionar').click(function () {
            console.log(this);
            $('#ajaxModel2').modal('show');
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
