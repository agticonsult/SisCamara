<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SisCamara</title>
    <link rel="shortcut icon" type="svg" href="{{ asset('image/layer-group-solid.svg') }}" style="color: #4a88eb">
    <link rel="shortcut icon" type="svg" href="{{ asset('image/layer-group-solid.svg') }}" style="color: #4a88eb">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.css"/>
    <link href="{{asset('select2-4.1.0/dist/css/select2.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('select2-bootstrap/dist/select2-bootstrap.css')}}"/>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

    <style>
        .error{
            color:red
        }
    </style>

</head>
<body>
    <div class="main d-flex justify-content-center w-100">
        <nav class="navbar navbar-expand-md shadow-sm" style="background-color: #0f4e96">
            <div class="container">
                <a class="sidebar-brand" href="{{ url('/') }}">
                    <div class="max-width">
                        <div class="imageContainer">
                            <img src="{{ 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('imagens/logo.png'))) }}" class="img-thumbnail" width="80px" height="60px" alt="">
                            <span class="align-middle mr-3" style="font-size: .999rem;">SisCamara</span>
                        </div>
                    </div>
                </a>
            </div>
        </nav>
        <main class="content d-flex p-0">
            <div class="container d-flex flex-column">
                <div class="row h-100">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">
                            <div class="text-center mt-4">
                                <h1 class="h2">
                                    Registrar-se
                                </h1>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="m-sm-4">

                                        <form action="{{route('registrar_store')}}" method="POST" id="form" class="form_prevent_multiple_submits">
                                            @csrf
                                            @method('POST')
                                            @include('sweetalert::alert')

                                            <div class="m-sm-4">
                                                <div class="mb-3">
                                                    <label class="form-label">*Nome</label>
                                                    <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe seu nome" value="{{ old('nome') }}">
                                                    @error('nome')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">*CPF</label>
                                                    <input class="form-control @error('cpf') is-invalid @enderror" type="text" name="cpf" id="cpf" placeholder="Informe seu CPF" value="{{ old('cpf') }}">
                                                    @error('cpf')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">*Email</label>
                                                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Informe um email válido" value="{{ old('email') }}">
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">*Data de nascimento</label>
                                                    <input class="dataFormat form-control @error('dt_nascimento_fundacao') is-invalid @enderror" type="date" name="dt_nascimento_fundacao" id="dt_nascimento_fundacao" min="1899-01-01" max="2000-13-13" value="{{ old('dt_nascimento_fundacao') }}">
                                                    @error('dt_nascimento_fundacao')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Celular/Telefone</label>
                                                    <input class="telefone form-control @error('telefone_celular') is-invalid @enderror" type="text" name="telefone_celular" value="{{ old('telefone_celular')}}">
                                                    @error('telefone_celular')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Celular/Telefone Recado</label>
                                                    <input class="telefone form-control @error('telefone_celular2') is-invalid @enderror" type="text" name="telefone_celular2" value="{{ old('telefone_celular2')}}">
                                                    @error('telefone_celular2')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">*Senha (mínimo 6 caracteres e máximo 35 caracteres)</label>
                                                    <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Informe uma senha">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Confirme a senha</label>
                                                    <input class="form-control @error('confirmacao') is-invalid @enderror" type="password" name="confirmacao" placeholder="Confirme a senha">
                                                    @error('confirmacao')
                                                        <div class="invalid-feedback">{{ $message }}</div><br>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <button type="submit" class="btn btn-lg btn-primary" style="width: 100%">Cadastrar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-12 text-right">
                        <p class="mb-0">
                            © <?php echo date('Y'); ?> - <a href="http://agile.inf.br" target="_blank" class="text-muted">Agile Tecnologia</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{asset('jquery-mask/dist/jquery.mask.min.js')}}"></script>
<script src="{{ url('js/fontawesome.js') }}"></script>
<script src="{{ url('js/bootstrap.js') }}"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ url('js/functions.js') }}"></script>
<script src="{{ url('js/prevent_multiple_submits.js') }}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8-beta.17/inputmask.js" integrity="sha512-XvlcvEjR+D9tC5f13RZvNMvRrbKLyie+LRLlYz1TvTUwR1ff19aIQ0+JwK4E6DCbXm715DQiGbpNSkAAPGpd5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{asset('select2-4.1.0/dist/js/select2.min.js')}}"></script>
@yield('scripts')
<script>
    $('#cpf').mask('000.000.000-00');

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

    function maskInputs() {
        var input = document.getElementsByClassName('telefone')
        var im = new Inputmask(
            {
                mask: ['(99)9999-9999', '(99)99999-9999'],  keepStatic: true
            }
        )
        im.mask(input)
    }
    maskInputs();

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
    });
</script>
</body>
</html>




