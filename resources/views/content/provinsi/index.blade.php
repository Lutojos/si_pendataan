<x-layout.app-admin title="{!! __('Provinsi') !!}">

    <div class="row">
        <div class="col-12">
            @include('components.layout.message-admin')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! __('Provinsi') !!}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" id="search-box" class="form-control" placeholder="Kata Kunci..." />
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="search-btn" class="btn btn-default">Cari</button>
                        </div>
                        <div class="col-md-9 text-right">
                            @if (auth()->user()->can('create provinsi'))
                                <a href="{{ route('provinsi.create') }}" type="button" class="btn btn-success">
                                    Tambah {!! __('Provinsi') !!}
                                </a>
                            @endif
                        </div>
                    </div>
                    <br />

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tbl-provinsi" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Nama Provinsi') }}</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <br>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript">
            $(function() {

                var table = $('#tbl-provinsi').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    order: [
                        [2, "desc"]
                    ],
                    searching: false,
                    lengthChange: false,
                    ajax: {
                        url: "{{ route('provinsi.list') }}",
                        type: "POST",
                        data: function(d) {
                            d.search = $("#search-box").val()
                            d['order'].forEach(function(items, index) {
                                d['order'][index]['column_name'] = d['columns'][items.column][
                                    'data'
                                ];
                            });
                        },
                        async: false
                    },
                    columns: [{
                            data: "provinsi_name",
                            name: "provinsi_name"
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                    ]
                });


                $('#search-btn').on('click', function() {
                    table.draw();
                });
            });
        </script>
    @endpush
</x-layout.app-admin>
