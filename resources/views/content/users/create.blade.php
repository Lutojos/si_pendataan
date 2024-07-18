<form action="" id="form-data">

    <div class="modal-header">
        <h5 class="modal-title">Form Add Staff</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <div id="alertBox"></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder=""
                            aria-describedby="helpId">

                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder=""
                            aria-describedby="helpId">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder=""
                            aria-describedby="helpId">

                    </div>
                    <div class="form-group">
                        <label for="">Role</label>
                        <select name="role" id="role" class="select2 form-control">
                            <option value="">--Pilih--</option>
                            @foreach ($role as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>


                <div class="col-md-12" id="for_role_4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                                <option value="">-</option>
                                <option value="1">Laki - laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Lahir</label>
                            <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                placeholder="" aria-describedby="helpId">

                        </div>
                        <div class="form-group">
                            <label for="">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control"
                                placeholder="" aria-describedby="helpId">

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" placeholder=""
                                aria-describedby="helpId">

                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control" placeholder=""
                                aria-describedby="helpId">
                        </div>

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group label-input-file">
                        <label for="" class="">Avatar</label>
                        <input type="file" name="avatar" id="avatar" class="form-control" placeholder=""
                            aria-describedby="helpId">

                    </div>
                </div>
                <div class="col-md-12" id="ktp_foto">
                    <div class="form-group label-input-file">
                        <label for="" class="">Foto KTP</label>
                        <input type="file" name="ktp" id="ktp" class="form-control" placeholder=""
                            aria-describedby="helpId">

                    </div>
                </div>
            </div>
            <div class="row">

            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="submit">Save</button>
        <div class="spinner-border text-primary" role="status" id="spin" style="display: none">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</form>

<script>
    $( 'input[name="tanggal_lahir"]' ).daterangepicker( {
        autoApply: true,
        singleDatePicker: true,
        showDropdowns: true,
        maxYear: parseInt( moment().format( 'YYYY' ), 10 ),
        locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: 'Clear'
        }
    } );
    $( "#properties" ).hide();
    $( "#for_role_4" ).hide();
    $( "#ktp_foto" ).hide();
    $( "#role" ).change( function () {
        if ( $( this ).val() == 2 || $( this ).val() == 4 ) {
            $( "#properties" ).show();

        } else {
            $( "#properties" ).hide();
        }
    } );
    $( "#role" ).change( function () {
        if ( $( this ).val() == 4 ) {
            $( "#ktp_foto" ).show();
            $( "#for_role_4" ).show();

        } else {
            $( "#ktp_foto" ).hide();
            $( "#for_role_4" ).hide();
        }
    } );
    $( ".select2" ).select2( {
        placeholder: "Pilih",
        allowClear: true
    } );


    $( "#form-data" ).submit( function ( e ) {
        e.preventDefault();
        let postData = new FormData();
        $.each( $( '#form-data :input' ).serializeObject(), function ( x, y ) {
            postData.append( x, y );
        } );
        // postData.append( 'ktp', 'undifined' );
        if ( $( '#ktp' )[ 0 ].files[ 0 ] ) {
            postData.append( 'ktp', $( '#ktp' )[ 0 ].files[ 0 ] );

        } else {
            postData.append( 'ktp', 'undifined' );
        }
        // postData.append( 'avatar', 'undifined' );

        if ( $( '#avatar' )[ 0 ].files[ 0 ] ) {
            postData.append( 'avatar', $( '#avatar' )[ 0 ].files[ 0 ] );

        } else {
            postData.append( 'avatar', 'undifined' );
        }
        ajax( {
            url: "{{ route('user.store') }}",
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
