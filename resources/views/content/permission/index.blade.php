<x-layout.app-admin title="Hak Akses">
    <div class="row">
        <div class="col-12">
            @include('components.layout.message-admin')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! __('Hak Akses ' . $role->name) !!}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tbl-msg"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" id="search-box" class="form-control" placeholder="Cari Modul..." />
                        </div>
                        <div class="col-md-10 text-right"></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-12">
                            <table id="tbl-permission" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="check-all">
                                            </div>
                                        </td>
                                        <th>{{ __('Modul') }}</th>
                                        <th colspan="5" class="text-center">{{ __ ('Hak Akses')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permissions as $module => $permission)
                                    <tr data-module="{{ $module }}">
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input check-row" data-module="{{ $module }}">
                                            </div>
                                        </td>
                                        <td>{{ $module }}</td>
                                        @foreach($permission as $item)
                                        @if(in_array($item->capabilities, $capabilities))
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input role-permission" {{ (in_array($item->name, $role_permissions)) ? 'checked' : '' }} value="{{ $item->name }}">
                                                <label for="" class="form-check-label">{{ $item->capabilities }}</label>
                                            </div>
                                        </td>
                                        @endif
                                        @endforeach
                                        <td>
                                            @foreach($permission as $item)
                                            @if(!in_array($item->capabilities ,$capabilities))
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input role-permission" {{ (in_array($item->name, $role_permissions)) ? 'checked' : '' }} value="{{ $item->name }}">
                                                <label for="" class="form-check-label">{{ $item->capabilities }}</label>
                                            </div>
                                            @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6">{{ __('Empty Data') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <i>* Silakan logout dan login kembali untuk mendapatkan efek perubahan permission telah dilakukan.</i>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(function() {
        // search by module name
        $(document).on('keyup', '#search-box', function() {
            let val = $(this).val();
            val = val.toLowerCase();

            $.each($('#tbl-permission > tbody > tr'), function(i, tr) {
                let module_row = $(tr).data('module');
                module_row = module_row.toLowerCase();
                if (val == '') {
                    // show all rows
                    $(tr).show();
                } else {
                    // filter by input search
                    if (module_row.indexOf(val) == -1) {
                        $(tr).hide();
                    }
                }
            })
        });


        //  check/uncheck all
        $('#check-all').on('change', function(){
            let val = $(this).is(':checked');
            $('.form-check-input').prop('checked', val);
        });

        // check/uncheck row
        $('.check-row').on('change', function() {
            let val = $(this).is(':checked');
            let module_name = $(this).data('module');
            let el = $('#tbl-permission > tbody > tr[data-module="'+module_name+'"] > td > div > .form-check-input');
            $.each(el, function(i, input) {
                $(input).prop('checked', val);
            });
        });

        @can('assign permission')
        // save permission
        $('.form-check-input').on('change', function() {
            let total = $('.role-permission').length;
            let checked = $('.role-permission:checked').length;
            let postData = [];
            if (checked > 0) {
                if (total != checked) {
                    $('#check-all').prop('checked', false);
                } else {
                    $('#check-all').prop('checked', true);
                }

                $.each($('.role-permission:checked'), function(i, input) {
                    postData.push($(input).val());
                });
            } else {
                $('#check-all').prop('checked', false);
            }

            // ajax request save permission
            $.ajax({
                url:"{{ route('role.permission.assign') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    role: "{{ $token }}",
                    permissions: postData
                },
                async: false,

                success: function(response) {
                    let html = '';
                    $('.tbl-msg').empty().fadeIn();
                    if (response.status == true) {
                        html = `<div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    ${response.message}
                                </div>`;
                    } else {
                        html = `<div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    ${response.message}
                                </div>`;
                    }
                    $('.tbl-msg').html(html).delay(500).fadeOut(400);
                }
            });
        });
        @endcan
    });
    </script>
    @endpush
</x-layout.app-admin>
