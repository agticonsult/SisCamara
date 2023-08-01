<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ágile</title>

    <link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">
    <script defer src="{{asset('js/fontawesome-all.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/dashboard.css')}}">

    <script src="{{asset('js/jquery.js')}}"></script>

</head>
<body>

<div class="container-fluid">

    <div class="container text-center" style="margin-bottom: 13.8%; margin-top: 13.8%;">

        <div class="row mt-5">
            <div class="col-sm">
                <a href="{{url('publica')}}">
                    <button class="btn btn-outline-primary">Web pública</button>
                </a>
            </div>
            <div class="col-sm">
                <a href="">
                    <button class="btn btn-outline-primary">Votação</button>
                </a>
            </div>
            <div class="col-sm">
                <a href="{{ route('login') }}">
                    <button class="btn btn-outline-primary">SisCamara</button>
                </a>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-sm">
                <a href="{{ url('atos/publica') }}">
                    <button class="btn btn-outline-primary">Atos</button>
                </a>
            </div>
            <div class="col-sm">
                <a href="{{ url('esic/cadastro') }}">
                    <button class="btn btn-outline-primary">E-SIC</button>
                </a>
            </div>
            {{-- <div class="col-sm">
                <a href="">
                    <button class="btn btn-outline-primary">SysCa</button>
                </a>
            </div> --}}
        </div>


    </div>

</div>

<!-- Footer -->
<footer class="page-footer font-small blue pt-4 mt-3" style="background-color: #1E90FF">

    <div class="text-center text-white mt-2">
        <h1 class="lead display-5">CÂMARA MUNICIPAL DE CORUMBÁ</h1>
        <p class="lead">(67) 3231-6570 | secretaria@camaracorumba.ms.gov.br</p>
    </div>

    <!-- Copyright -->
    <div class="footer-copyright text-center text-white py-3">© <?php echo date('Y'); ?> Copyright:
        <a href="http://agile.inf.br/" target="_blank" class="text-white"> Ágile</a>
    </div>
    <!-- Copyright -->

</footer>
<!-- Footer -->


<script src="{{asset('js/jquery-3.2.1.slim.min.js')}}"></script>
<script src="{{asset('js/popper.min.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/dashboard.js')}}"></script>
</body>
</html>
