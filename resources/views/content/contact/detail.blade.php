<x-layout.app-admin title="{!! __('Hubungi Kami') !!}">
    <style>
        .chating {
            transform: translate(0, 0);
            overflow: auto;
            height: 600px;
            padding: 10px;
        }

    </style>
    <div class="row">
        <div class="col-12">
            <div class="card direct-chat direct-chat-primary">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">Direct Chat</h3>

                </div>

                <div class="card-body">

                    <div class="chating" id="contentJs">

                    </div>

                </div>

                <div class="card-footer">
                    <form action="#" method="post" id="form_pesan">
                        <div class="input-group">
                            <input type="hidden" name="to" value="{{ $data[0]['to'] }}" id="to">
                            <input type="hidden" name="token_pesan" value="{{ $token }}" id="from">
                            <input type="text" name="message" id="message" placeholder="Type Message ..."
                                class="form-control">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </span>
                        </div>
                        @method('PUT')
                    </form>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function convertUtcToLocalTime( DateTime ) {
            //get utc time
            const UtcTime = moment.utc( DateTime );
            // Mendapatkan zona waktu klien
            const clientTimeZone = moment.tz.guess();
            // Mengubah waktu UTC ke zona waktu klien
            const localTime = UtcTime.clone().tz( clientTimeZone );
            // Mengembalikan waktu lokal dalam format string
            return localTime.format( 'DD MMMM YYYY HH:mm' );
        }
        var site_url = "{{ url('/') }}";
        var page = 1;
        var token = "{{ $token }}";

        load_more( page );

        $( document ).on( 'click', '#load_more', function () {
            page++;
            load_more( page );
        } );
        //load prev
        $( document ).on( 'click', '#load_prev', function () {
            page--;
            load_more( page );
        } );

        function load_more( page ) {
            ajax( {
                type: "GET",
                url: site_url + '/contact/' + token + '/edit?page=' + page + '&nocache = ' + Math.random(),
                alert: false,
                dataType: "html",
                postData: {
                    id: '{{$token}}',
                },
                success: function ( ret ) {
                    //if data length 0

                    $( '#contentJs' ).html( ret );


                },
            } );


        }


        $( "#form_pesan" ).submit( function ( e ) {
            e.preventDefault();
            let postData = new FormData();
            $.each( $( '#form_pesan :input' ).serializeObject(), function ( x, y ) {
                postData.append( x, y );
            } );

            ajax( {
                url: "{{ route('contact.update',$token) }}",
                postData: postData,
                processData: false,
                contentType: false,
                alert: false,
                beforeSend: function () {
                    $( "#submit" ).attr( 'style', "display:none" );
                    $( "#spin" ).attr( 'style', "display:block" );
                },
                success: function ( response ) {
                    //get page param url
                    $( "#message" ).val( " " );
                    var last_page = response.data.last_page;
                    load_more( last_page )

                },
                error: function ( err ) {
                    loadAlert( err.msg, true );
                    $( "#submit" ).attr( 'style', "display:block" );
                    $( "#spin" ).attr( 'style', "display:none" );
                },
                failure: function ( err ) {
                    loadAlert( err.msg, true );
                }
            } );
        } );

    </script>
    @endpush
</x-layout.app-admin>
