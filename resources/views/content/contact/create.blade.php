<form action="" id="form-data">

    <div class="modal-header">
        <h5 class="modal-title">Form Kirim Pesan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <div id="alertBox"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Tujuan</label>
                        <select name="to" id="to" class="select2 form-control">


                        </select>

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Pesan</label>
                        <textarea name="message" id="message" cols="30" rows="10" class="form-control"></textarea>

                    </div>
                </div>



            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="submit">Kirim</button>
        <div class="spinner-border text-primary" role="status" id="spin" style="display: none">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</form>

<script>
    $( "#properties" ).hide();
    $( "#ktp_foto" ).hide();
    $( "#role" ).change( function () {
        if ( $( this ).val() == 2 ) {
            $( "#properties" ).show();

        } else {
            $( "#properties" ).hide();
        }
    } );
    $( "#role" ).change( function () {
        if ( $( this ).val() == 4 ) {
            $( "#ktp_foto" ).show();

        } else {
            $( "#ktp_foto" ).hide();
        }
    } );
    $( '#to' ).select2( {
        placeholder: "Pilih Tujuan...",
        //minimumInputLength: 1,
        allowClear: true,
        multiple: false,
        ajax: {
            url: "{{ route('contact.option') }}",
            dataType: 'json',
            data: function ( params ) {
                return {
                    q: $.trim( params.term )
                };
            },
            processResults: function ( data ) {
                var json = [];
                //append the results to the select2 ALL option
                //count data
                var total_data = data.data.length;
                if ( total_data > 0 ) {
                    json.push( {
                        id: 'all',
                        text: 'Semua Pelanggan'
                    } );
                }


                $.each( data.data, function ( i, obj ) {
                    json.push( {
                        id: obj.id,
                        text: obj.name
                    } );
                } );
                return {
                    results: json
                };

            },
            cache: true
        }
    } );


    $( "#form-data" ).submit( function ( e ) {
        e.preventDefault();
        let postData = new FormData();
        $.each( $( '#form-data :input' ).serializeObject(), function ( x, y ) {
            postData.append( x, y );
        } );
        postData.append( 'send_from', 2 );
        ajax( {
            url: "{{ route('contact.store') }}",
            postData: postData,
            processData: false,
            contentType: false,
            alert: false,
            beforeSend: function () {
                $( "#submit" ).attr( 'style', "display:none" );
                $( "#spin" ).attr( 'style', "display:block" );
            },
            success: function ( response ) {
                alert( response.msg );
                $( "#myModal" ).modal( "hide" );
                $( '#tbl-user' ).DataTable().ajax.reload();

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
