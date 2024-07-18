<x-layout.app-admin title="{!! __('Edit Kecamatan') !!}">
    <div class="row" style="margin-left: 8px; margin-right: 8px;">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('kecamatan.update', ['token' => $datas->_token]) }}" method="POST" id="form-edit"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @include('components.layout.message-admin')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Provinsi<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2-provinsi" name="provinsi_id"
                                                id="provinsi_id">
                                                <option value="">Pilih Provinsi</option>
                                                @foreach ($provinsi as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ $datas->provinsi_id == $row->id ? 'selected' : '' }}>
                                                        {{ $row->provinsi_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Kota<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2-kota" name="kota_id"
                                                id="kota_id">
                                                <option value="">Pilih Kota</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Kecamatan<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="kecamatan_name" id="kecamatan_name"
                                                class="form-control" value="{{ old('kecamatan_name', $datas->kecamatan_name) }}"
                                                placeholder="Nama Kecamatan">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info float-right"
                                        id="btn_save">Simpan</button>
                                    <a href="{{ route('kecamatan.index') }}" type="button"
                                        class="btn btn-default float-right mr-2">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.card-body -->
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            $("#form-edit").submit(function(e) {
                e.preventDefault();
                var form = document.getElementById("form-edit");

                let postData = new FormData();
                $.each($('#form-edit :input').serializeObject(), function(x, y) {
                    postData.append(x, y);
                });

                processingform(form);
                ajax({
                    url: $("#form-edit").attr("action"),
                    postData: postData,
                    processData: false,
                    contentType: false,
                    alert: false,
                    success: function(response) {

                        alert(response.msg);
                        unprocessingform(form);
                        window.location.href = "{{ route('kecamatan.index') }}";

                    },
                    error: function(err) {
                        unprocessingform(form);
                        loadAlert(err.msg, true);
                    },
                    failure: function(err) {
                        unprocessingform(form);
                        loadAlert(err.msg, true);
                    }
                });

            });

            $(document).ready(function() {
                $('.select2-provinsi').select2({
                    minimumInputLength: 0,
                    allowClear: true,
                    placeholder: 'Masukkan nama provinsi',
                    theme: 'bootstrap4',
                    ajax: {
                        dataType: 'json',
                        url: "{{ route('provinsi.option') }}",
                        delay: 800,
                        data: function(params) {
                            return {
                                search: params.term
                            }
                        },
                        processResults: function(data, page) {
                            return {
                                results: data
                            };
                        },
                    }
                }).on('select2-provinsi:select', function(evt) {
                    var data = $(".select2-provinsi option:selected").text();
                    var value = $(".select2-provinsi option:selected").val();
                    loadKota('kota_id', value)

                }).on('select2-provinsi:clear', function(evt) {
                    $('.select2-kota').empty();
                });

                $('.select2-provinsi').on('change', async function() {
                    $.when(loadKota($('#kota_id'), $(this).val(), )).done(function(
                        kota) {});
                });

                loadKota( $( '#kota_id' ), "{{ $datas->provinsi_id }}", "{{ $datas->kota_id }}" );

                function loadKota(target, provinsi_id, selected_id = null) {
                    let option = "<option value=\"\">-- Pilih Kota --</option>";

                    if (!provinsi_id) {
                        target.html(option);
                        return
                    }

                    return ajax({
                        type: "GET",
                        url: "{{ route('kota.option') }}?provinsi_id=" + provinsi_id,
                        alert: false,
                        success: function(ret) {
                            let data = ret;
                            for (let i = 0; i < data.length; i++) {
                                if (selected_id != "" && (data[i].id == selected_id)) {
                                    selected = "selected";
                                } else {
                                    selected = ""
                                }
                                option +=
                                    `<option value="${data[i].id}" ${selected}> ${data[i].text} </option>`
                            }

                            target.html(option);

                        },
                    })
                }
            });
        </script>
    @endpush
</x-layout.app-admin>
