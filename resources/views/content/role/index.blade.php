<x-layout.app-admin title="Role">
    <div class="row">
        <div class="col-12">
            @include('components.layout.message-admin')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! __('Role') !!}</h3>
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
                        <div class="col-md-9 text-right d-none">

                            @can('create role')
                            <a href="{{ route('role.create') }}" type="button" class="btn btn-success">
                                Tambah {!! __('Role') !!}
                            </a>
                            @endcan

                        </div>
                    </div>
                    <br />

                    <div class="row">
                        <div class="col-12">
                            <table id="tbl-role" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Role Name') }}</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var table;
        $( function () {
            table = $( '#tbl-role' ).DataTable( {
                ajax: {
                    type: 'POST',
                    url: '{{ route("role.list") }}',
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
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "id",
                        name: "action",
                        orderable: false,
                        render: function ( data, type, row, meta ) {
                            let btn_group = '';
                            btn_group += `<div class="btn btn-group">`;
                            @can( 'assign permission' )
                            btn_group += `<a href="{{ route('role.permission.index') }}/${row._token}" class="btn btn-sm btn-warning"
                                    title="Hak Akses">
                                    <i class="fas fa-key"></i> Hak Akses
                                </a>`;
                            @endcan
                            @can( 'edit role' )
                            btn_group += `<a href="{{ route('role.edit') }}/${row._token}" class="btn btn-sm btn-default d-none"
                                    title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>`;
                            @endcan
                            @can( 'delete role' )
                            btn_group += `<a href="{{ route('role.delete') }}/${row._token}" class="btn btn-sm btn-danger d-none"
                                    title="Delete" onclick="return confirm('Delete Role?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>`;
                            @endcan
                            btn_group += `</div>`;
                            return btn_group;
                        }
                    },
                ]
            } );


            $( '#search-btn' ).on( 'click', function () {
                table.draw();
            } );
        } );

    </script>
    @endpush
</x-layout.app-admin>
