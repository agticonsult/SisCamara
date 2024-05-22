@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Alteração de Usuário</h1>
    <div class="card" style="background-color:white">
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
                                    <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" value="{{ $usuario->pessoa->nome != null ? $usuario->pessoa->nome : old('nome') }}">
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Email</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ $usuario->email != null ? $usuario->email : old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*CPF</label>
                                    <input class="cpf form-control @error('cpf') is-invalid @enderror" type="text" name="cpf" id="cpf" value="{{ $usuario->cpf != null ? $usuario->cpf : old('cpf') }}">
                                    @error('cpf')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Data de Nascimento</label>
                                    <input class="dataFormat form-control @error('dt_nascimento_fundacao') is-invalid @enderror" type="date" min="1899-01-01" max="2000-13-13" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" value="{{ $usuario->pessoa->dt_nascimento_fundacao != null ? $usuario->pessoa->dt_nascimento_fundacao: old('dt_nascimento_fundacao') }}">
                                    @error('dt_nascimento_fundacao')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Perfil</label>
                                    <select class="select_multiple form-control @error('id_perfil') is-invalid @enderror" name="id_perfil[]" multiple>
                                        @foreach ($perfils as $perfil)
                                            @php
                                                $temPerfil = 0;

                                                foreach ($usuario->permissoes_ativas as $permissao){
                                                    if ($permissao->id_perfil == $perfil->id){
                                                        $temPerfil = 1;
                                                    }
                                                }
                                            @endphp
                                            @if ($temPerfil == 1)
                                                <option value="{{ $perfil->id }}" selected>{{ $perfil->descricao }}</option>
                                            @else
                                                <option value="{{ $perfil->id }}">{{ $perfil->descricao }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('id_perfil')
                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        <a href="{{ route('usuario.index') }}" class="btn btn-secondary">Voltar</a>
                    </div>
                    <br>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md-12">
                <hr><br>
                <h3>Listagem de Perfis do usuário:  <strong>{{ $permissao->user->id_pessoa != null ? $permissao->user->pessoa->nome : 'não informado' }} - {{ $permissao->user->email != null ? $permissao->user->email : 'não informado' }}</strong></h3>
                <br>
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                {{-- <th scope="col">Usuário</th>
                                <th scope="col">CPF</th>
                                <th scope="col">Email</th> --}}
                                <th scope="col">Perfil (clique no botão para ver as funcionalidades do perfil)</th>
                                <th scope="col">Cadastrado por</th>
                                <th scope="col">Status <br>(para desativar este perfil deste usuário, clique no botão "Ativo")</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuario->permissoes as $permissao)
                                <tr>
                                    {{-- <td>{{ $permissao->user->id_pessoa != null ? $permissao->user->pessoa->nome : 'não informado' }}</td>
                                    <td class="cpf">{{ $permissao->user->cpf != null ? $permissao->user->cpf : 'não informado' }}</td>
                                    <td>{{ $permissao->user->email != null ? $permissao->user->email : 'não informado' }}</td> --}}
                                    <td >
                                        <button  type="button" class="funcionalidades btn btn-dark" id="{{ $permissao->id_perfil }}" name="{{ $permissao->perfil->descricao }}" style="width: 100%">
                                            {{ $permissao->perfil->descricao }}
                                        </button>
                                    </td>
                                    <td>
                                        <strong>{{ $permissao->cadastradoPorUsuario != null ? $permissao->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                        em <strong>{{ $permissao->created_at != null ? $permissao->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        @switch($permissao->ativo)
                                            @case(1)
                                                <button type="button" class="desativar btn btn-success" name="{{ $permissao->id }}" id="{{ $permissao->perfil->descricao }}" style="width: 100%">
                                                    Ativo
                                                </button>
                                                @break
                                            @default
                                                <button type="button" class="btn btn-info">
                                                    Desativado
                                                    por <strong>{{ $permissao->inativadoPorUsuario != null ? $permissao->inativadoPor->pessoa->nome : 'não informado' }}</strong>
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

@endsection

@section('scripts')
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

            $('.select_multiple').select2({
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    }
                },
                closeOnSelect: false,
                width: '100%',
                dropdownCssClass: "bigdrop"
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
