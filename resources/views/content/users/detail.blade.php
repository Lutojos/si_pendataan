<form action="" id="form-data">

    <div class="modal-header">
        <h5 class="modal-title">Form Detail Staff</h5>
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
                        <label for="">Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="" disabled
                            value="{{ $data->name??"-" }}">

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="" disabled
                            value="{{ $data->email ??"-"}}">

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Role </label>
                        <select name="role" id="role" class="form-control" disabled>
                            <option value="">--Pilih--</option>
                            @foreach ($role as $role)
                            <option value="{{ $role->id }}" {{ $data->roles_id==$role->id?'selected':'' }}>
                                {{ $role->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="col-md-12" id="for_role_4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" disabled>
                                <option value="">-</option>
                                <option value="1" {{ $data->gender==1?'selected':'' }}>Laki - laki</option>
                                <option value="2" {{ $data->gender==2?'selected':'' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Lahir</label>
                            <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control" disabled
                                placeholder="" value="{{ date('d/m/Y',strtotime($data->birthofdate)) }}">

                        </div>
                        <div class="form-group">
                            <label for="">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" disabled
                                value="{{ $data->phone_number }}">

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" placeholder=""
                                value="{{ $data->birthofplace }}" disabled>

                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control" disabled
                                value="{{ $data->address }}">
                        </div>

                    </div>
                </div>
               
                <div class="col-md-12">
                    <div class="form-group label-input-file">
                        <label for="" class="">Avatar</label>
                        <input type="file" name="avatar" id="avatar" class="form-control" placeholder="" disabled
                            aria-describedby="helpId">
                        {{-- image avatar --}}
                        <img src="{{ $data->getAvatar() }}" alt="" width="100px" height="100px" id="avatar-img">

                    </div>
                </div>
                <div class="col-md-12" id="ktp_foto">
                    <div class="form-group label-input-file">
                        <label for="" class="">Foto KTP</label>
                        <input type="file" name="ktp" id="ktp" class="form-control" placeholder="" disabled
                            aria-describedby="helpId">
                        {{-- image  --}}
                        <img src="{{ $data->getKtp() }}" alt="" width="100px" height="100px" id="ktp-img">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

        <div class="spinner-border text-primary" role="status" id="spin" style="display: none">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    @method('PUT')
</form>

<script>
    $( "#properties" ).hide();
    $( "#ktp_foto" ).hide();
    $( "#for_role_4" ).hide()
    $( "#for_role_3" ).hide()
    //input disabeld

    var role = "{{ $data->role_id }}";
    if ( role == 2 || role == 4 ) {
        $( "#properties" ).show();
    }
    if ( role == 4 ) {
        $( "#ktp_foto" ).show();
        $( "#for_role_4" ).show();

    }
    if ( role == 3 ) {
        $( "#for_role_4" ).show();
        $( "#for_role_3" ).show();
        $( "#ktp_foto" ).show();
    }

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


    $( "#form-data" ).submit( function ( e ) {
        e.preventDefault();
        let postData = new FormData();
        $.each( $( '#form-data :input' ).serializeObject(), function ( x, y ) {
            postData.append( x, y );
        } );
        postData.append( 'ktp', 'undifined' );
        if ( $( '#ktp' )[ 0 ].files[ 0 ] ) {
            postData.append( 'ktp', $( '#ktp' )[ 0 ].files[ 0 ] );

        }
        postData.append( 'avatar', 'undifined' );
        if ( $( '#avatar' )[ 0 ].files[ 0 ] ) {
            postData.append( 'avatar', $( '#avatar' )[ 0 ].files[ 0 ] );

        }
        postData.append( '_token_user', '{{ $data->_token }}' );
        ajax( {
            url: "{{ route('user.update',$data->_token) }}",
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
