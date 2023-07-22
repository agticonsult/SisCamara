<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W5RR4PC');</script>
    <!-- End Google Tag Manager -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"> </script> <script>   window.dataLayer = window.dataLayer || [];   function gtag(){dataLayer.push(arguments);}   gtag('js', new Date());   gtag('config', 'UA-219660591-1');   gtag('config','AW-10850169212'); </script>
	<!-- Global site tag (gtag.js) - Google Analytics -->

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>Módulo de documentos</title>
	<link rel="shortcut icon" type="svg" href="{{ asset('image/layer-group-solid.svg') }}" style="color: #4a88eb">
	{{-- Styles --}}
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" integrity="sha512-2L0dEjIN/DAmTV5puHriEIuQU/IL3CoPm/4eyZp3bM8dnaXri6lK8u6DC5L96b+YSs9f3Le81dDRZUSYeX4QcQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Fim Styles --}}

</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W5RR4PC"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <style>
        #voltar{
            color: black;
        }
        #voltar:hover{
            text-decoration: none;
            color: blue;
        }
    </style>
    <div class="wrapper">
        <div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
                        <li class="mr-2">
                            <a href="{{ route('passwordReset1') }}" class="btn btn-outline-primary"><strong style="color: black">Voltar</strong></a>
                        </li>
					</ul>
				</div>
			</nav>
            @include('errors.alerts')

            @if (isset($errors) && count($errors)>0)
                <div class="text-center mt-4 mb-4 p-2 alert-danger" >
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

			{{-- Main --}}
			<main class="content d-flex p-0">
                <div class="container d-flex flex-column">
                    <div class="row h-100">
                        <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                            <div class="d-table-cell align-middle">

                                <div class="text-center mt-4">
                                    <h1 class="h2">Alteração de senha</h1>
                                    {{-- <p class="lead">
                                        Para alterar sua senha insira as informações de login
                                    </p> --}}
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-sm-4">
                                            <h1 class="text-center">
                                                Este link está expirado! <br>
                                                <a href="{{ route('passwordReset1') }}" id="voltar">Voltar</a>
                                            </h1>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
			{{-- Fim Main --}}

			{{-- Footer --}}
			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						{{-- <div class="col-6 text-left">
							<ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('ticket.home') }}" class="btn btn-outline-primary ml-2"><strong>Tickets</strong></a>
                                </li>
							</ul>
						</div> --}}
						<div class="col-12 text-right">
							<p class="mb-0">
								&copy; 2022 - <a href="http://agile.inf.br" class="text-muted">Agile Tecnologia</a>
							</p>
						</div>
					</div>
				</div>
			</footer>
			{{-- Fim Footer --}}

		</div>{{-- Fim Div Main --}}
    </div>

</body>

{{-- Scripts --}}
{{-- <script src="{{ url('js/jquery.js') }}"></script> --}}
<script src="{{ url('js/main.js') }}"></script>
<script src="{{ url('js/app.js') }}"></script>
<script src="{{ url('js/fontawesome.js') }}"></script>
<script src="{{ url('js/functions.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js" integrity="sha512-bj8HE1pKwchoYNizhD57Vl6B9ExS25Hw21WxoQEzGapNNjLZ0+kgRMEn9KSCD+igbE9+/dJO7x6ZhLrdaQ5P3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{asset('js/jquery.js')}}"></script>

</html>

