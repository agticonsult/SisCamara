@extends('layout.main')

@section('content')

    @include('errors.alerts')

    <h1 class="h3 mb-3">Cadastro de Usuário</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <div class="m-sm-8">
                    <div class="row mb-0">
                        <div class="col-sm-6 mb-0">
                            <div class="card">
                                <div class="card-body" style="background-color: rgb(196, 216, 238)">
                                    <h5 class="card-title">Pessoa Física</h5>
                                    <a href="{{ route('usuario.createPessoaFisica') }}" class="btn btn-primary">Avançar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-0">
                            <div class="card">
                                <div class="card-body" style="background-color: rgb(196, 202, 209)">
                                    <h5 class="card-title">Pessoa Jurídica</h5>
                                    <a href="{{ route('usuario.createPessoaJuridica') }}" class="btn btn-secondary">Avançar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
