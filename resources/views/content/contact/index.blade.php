<x-layout.app-admin title="{!! __('Hubungi Kami') !!}">

    <div class="row">
        <div class="col-12">
            @include('components.layout.message-admin')
            <style>
                .unread {
                    background-color: #EEEEEE;
                }

            </style>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! __('Hubungi Kami') !!}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" id="search-box" class="form-control" placeholder="Keyword..." />
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="search-btn" class="btn btn-default">Cari</button>
                        </div>
                        <div class="col-md-9 text-right">
                            @if (auth()->user()->can('create contact us') )
                            <a href="{{ route('contact.create') }}" type="button" class="btn btn-success"
                                data-toggle="modal" data-target="#myModal">
                                <i class="fas fa-edit"></i> Tulis Pesan
                            </a>
                            @endif

                        </div>
                    </div>
                    <br />

                    <div class="row">
                        <div class="col-12">
                            <table id="tbl-user" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Dari') }}</th>
                                        <th>{{ __('Untuk') }}</th>
                                        <th>{{ __('Properti') }}</th>
                                        <th>{{ __('Last Message') }}</th>
                                        <th>{{ __('Diperbaharui') }}</th>

                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex">
                    <div class="text-truncate mr-auto">
                    </div>
                    <div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var table;
        const edit = "{{auth()->user()->can('edit contact us') }}";
        const del = "{{ auth()->user()->can('delete contact us') }}";
        $( function () {
            table = $( '#tbl-user' ).DataTable( {
                ajax: {
                    type: 'POST',
                    url: "{{ route('contact.list') }}",
                    data: function ( d ) {
                        d.filter = {
                            search: $( '#search-box' ).val()
                        }
                    }
                },
                processing: true,
                searching: false,
                lengthChange: false,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: true,
                order: [
                    [ 0, "desc" ]
                ],
                fnServerParams: function ( data ) {
                    data[ 'order' ].forEach( function ( items, index ) {
                        data[ 'order' ][ index ][ 'column' ] = data[ 'columns' ][ items
                            .column
                        ][ 'data' ];
                    } );
                },
                columns: [ {
                        data: "from",
                        name: "from"
                    }, {
                        data: "to",
                        name: "to",


                    }, {
                        data: "property",
                        name: "property",


                    },
                    {
                        data: "subject",
                        name: "subject"
                    }, {
                        data: "updated_at",
                        name: "updated_at",
                        render: function ( data, type, row, meta ) {
                            return row.updated_at
                        }
                    },


                    {
                        data: "id",
                        name: "action",
                        orderable: false,
                        render: function ( data, type, row, meta ) {
                            let btn_group = '';
                            let route = "{{ route('contact.edit',':token') }}".replace(
                                ':token', `${row.token_pesan}` );
                            btn_group += `<div class="btn btn-group">`;
                            if ( edit ) {
                                btn_group += `<a href="` + route +
                                    `" class="btn btn-sm btn-default" title="Edit dan balas">  <i class="fas fa-edit"></i> Lihat dan balas </a>`;
                            }
                            btn_group += `</div>`;
                            return btn_group;
                        }
                    },
                ],
                rowCallback: function ( row, data, index ) {
                    var unread = data.is_read;
                    //add class unread to row
                    if ( unread == 0 ) {
                        $( row ).addClass( 'unread' );
                    }
                },
                columnDefs: [ {
                    targets: [ 0, 1, 2, 3, 4 ],
                    className: 'text-center',

                } ]
            } );


            $( '#search-btn' ).on( 'click', function () {
                table.draw();
            } );
        } );

        function deleteData( id ) {
            //show confirm dialog
            if ( !confirm( "Are you sure to delete this data?" ) ) {
                return false;
            }
            $.ajax( {
                url: "{{ route('contact.destroy',':id') }}".replace( ':id', id ),
                type: 'DELETE',
                success: function ( result ) {

                    $( '#tbl-user' ).DataTable().ajax.reload();
                }
            } );
        }

    </script>
    @endpush
</x-layout.app-admin>
