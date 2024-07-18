<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }}</title>
        <link rel="icon" href="{{ asset('assets/img/logo-sambina.png') }}">

        {{-- meta csrf --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <!-- IonIcons -->
        <link rel="stylesheet" href="{{ asset('plugins/ionicons/ionicons.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/pikaday/pikaday.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/vendor/datatable/dataTables.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" />
        <style>
            .select2-container~.invalid-feedback {
                display: block;
            }

            .select2-container--default .select2-selection--single.is-invalid {
                border: 1px solid #f25961 !important;
            }

        </style>
    </head>

    <body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed text-sm">
        <div class="wrapper">
            <x-layout.navbar-admin />
            <x-layout.sidebar-admin />
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">{{ $title }}</h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active">{{ $title }}</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                {{ $slot }}
                <!-- Control Sidebar -->
                <aside class="control-sidebar control-sidebar-dark">
                    <!-- Control sidebar content goes here -->
                </aside>
                <!-- /.control-sidebar -->
                <!-- Modal -->
                <div class="modal fade" id="myModal" role="dialog" aria-hidden="true" data-backdrop="static"
                    data-keyboard="false">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">

                        </div>
                    </div>
                </div>
                <!-- Main Footer -->
                <footer class="main-footer text-sm">
                    <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ env('APP_NAME') }}</a>.</strong>
                    All rights reserved.
                    <div class="float-right d-none d-sm-inline-block">
                        <b>Version</b> 1.0.0
                    </div>
                </footer>
            </div>


        </div>
        <!-- jQuery -->
        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/moment/moment-timezone-with-data.min.js') }}"></script>
        <script src="{{ asset('plugins/pikaday/pikaday.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- AdminLTE -->
        <script src="{{ asset('dist/js/adminlte.js') }}"></script>
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('/vendor/slick-1.8.1/slick.min.js') }}"></script>
        <script src="{{ asset('/vendor/bootstrap-4.0.0-dist/js/bootstrap.bundle.js') }}"></script>
        <script src="{{ asset('/vendor/datatable/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/vendor/datatable/dataTables.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('/vendor/datatable/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
        <script src="{{ asset('assets/js/custom/global.fe.js') }}"></script>
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('mask/jquery.mask.min.js') }}"></script>
        <script lang="javascript">
            $.ajaxSetup( {
                headers: {
                    'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                },
                //if token expired
                statusCode: {
                    419: function () {
                        //redirect to login page
                        window.location.href = "/";
                    },
                    //not authorized
                    401: function () {
                        //redirect to login page
                        window.location.href = "/";
                    }
                }
            } );

            $( "#myModal" ).on( "show.bs.modal", function ( e ) {
                //hide myModal2
                $( "#myModal2" ).modal( 'hide' );
                var link = $( e.relatedTarget );
                $( this ).find( ".modal-content" ).load( link.attr( "href" ) );
            } );
            $( '#myModal' ).on( 'hidden.bs.modal', function () {
                // remove the bs.modal data attribute from it
                $( this ).removeData( 'bs.modal' );
                // and empty the modal-content element
                $( '#myModal .modal-content' ).empty();
            } );

            function convertServerTimeToLocalTime( dateTime ) {
                const serverTime = moment( dateTime );
                const clientZOne = moment.tz.guess();
                const localTime = moment.tz( serverTime, clientZOne );
                return localTime;
            }


            $( ".input-currency" ).on( "input", function () {
                if ( /^0/.test( this.value ) ) {
                    this.value = this.value.replace( /^0/, "" )
                }
            } );
            $( ".input-currency" ).mask( "#,##0", {
                reverse: true,
            } );

            $( ".input-persen" ).mask( "00,00", {
                reverse: true,
            } );

            $( '.input-phone' ).mask( "000000000000000000" );
            $( ".input-latitud" ).mask( "-#9.999999" );
            $( ".input-longitud" ).mask( "#99.999999" );

            $('.summernote').summernote({
                    codeviewFilter: false,
                    codeviewIframeFilter: true,
                    placeholder: 'Masukan Deskripsi',
                    tabsize: 2,
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear', 'color', 'fontname']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['codeview', 'help']]
                    ],
                    callbacks: {
                        onBlurCodeview: function(contents, $editable) {
                            var newContents = contents.replace(/alert/g, '').replace(/script/g, '');
                            $('.summernote').summernote('code', newContents);
                        }
                    }
            });
        </script>

        <script type="text/javascript"
            src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback=initMap"></script>
        @stack('scripts')
    </body>




</html>
