<link rel="stylesheet" href="{{ asset('maps/leaflet.css') }}" />

<link rel="stylesheet" href="{{ asset('maps/leaflet-locationpicker.css') }}" />
<x-layout.app-admin title="{!! __('Edit Anggota') !!}">
    <style>
        .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-selection__arrow {
            height: 38px !important;
        }

        input[type="file"] {
            display: block;
        }

        .imageThumb {
            max-height: 75px;
            border: 2px solid;
            padding: 1px;
            cursor: pointer;
        }

        .pip {
            display: inline-block;
            margin: 10px 10px 0 0;
            position: relative;
            width: auto;
            height: 100px;
        }

        .checkbox {
            right: initial;
            display: block;
            width: 16px;
            color: initial;
            background-color: #6eb4ff00;
            font-size: medium;
            justify-content: center;
            margin-left: 5px;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: right;
        }

        .remove {
            display: block;
            background: #444;
            border: 1px solid black;
            color: white;
            text-align: center;
            cursor: pointer;
        }

        .remove:hover {
            background: white;
            color: black;
        }

        .file-block {
            border-radius: 10px;
            background-color: rgba(144, 163, 203, 0.2);
            margin: 5px;
            color: initial;
            /* display: inline-flex; */
            display: inline-block;
            margin: 10px 10px 0 0;
            position: relative;
        }

        span.name {
            padding-right: 10px;
            width: max-content;
            display: inline-flex;
            display: none;
        }

        .file-delete {
            display: block;
            background: #444;
            border: 1px solid black;
            color: white;
            text-align: center;
            cursor: pointer;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            font-size: 10pt;

            &:hover {
                background-color: rgba(144, 163, 203, 0.2);
                border-radius: 10px;
            }

            &>span {
                transform: rotate(45deg);
            }
        }
    </style>
    <div class="row" style="margin-left: 8px; margin-right: 8px;">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('anggota.update', ['token' => $data->_token]) }}" method="POST" id="form-edit"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @include('components.layout.message-admin')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama<span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                value="{{ old('name', $data->name) }}" placeholder="Nama Anggota">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Umur<span class="text-danger">*</span></label>
                                            <input type="text" name="umur" id="umur" class="form-control"
                                                value="{{ old('umur', $data->umur) }}" placeholder="Umur Anggota">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nomor Telepon<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="phone_number" id="phone_number"
                                                class="form-control"
                                                value="{{ old('phone_number', $data->phone_number) }}"
                                                placeholder="Nomor Telepon">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Jenis Kelamin<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="gender" id="gender">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="Perempuan"
                                                    {{ $data->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan
                                                </option>
                                                <option value="Laki-laki"
                                                    {{ $data->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Provinsi<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2-provinsi" name="provinsi_id"
                                                id="provinsi_id">
                                                <option value="">Pilih Provinsi</option>
                                                @foreach ($provinsi as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ $data->provinsi_id == $row->id ? 'selected' : '' }}>
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
                                            <select class="form-control select2-kota" name="kota_id" id="kota_id">
                                                <option value="">Pilih Kota</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Kecamatan<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2-kecamatan" name="kecamatan_id"
                                                id="kecamatan_id">
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Desa<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2-desa" name="desa_id" id="desa_id">
                                                <option value="">Pilih Desa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Alamat Lengkap<span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="address" id="address">{{ old('address', $data->address) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <input id="geoloc5" type="hidden" value="{{ $data->latitude }},{{ $data->longitude }}" size="20" />
                                        <div id="fixedMapCont"
                                            style="border: 1px solid black; min-height: 140;min-width: 200;"></div>
                                        <input type="hidden" name="latitude" id="latitude" class="form-control"
                                            value="{{ old('latitude', $data->latitude) }}" placeholder="Latitude">
                                        <input type="hidden" name="longitude" id="longitude" class="form-control"
                                            value="{{ old('longitude', $data->longitude) }}" placeholder="Longitude">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Gambar Profil<span
                                                    class="text-danger">*</span></label>
                                            <input type="file" name="image_path" id="image_path"
                                                class="form-control">
                                            <img src="{{ $data->getProfil() }}" alt="" width="100px"
                                                height="100px" id="avatar-img">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Gambar <span class="text-danger">*</span></label>
                                            <div class="field" align="left">
                                                <label for="files">
                                                    <a class="btn btn-primary text-light btn-sm" role="button"
                                                        aria-disabled="false">+
                                                        Upload Gambar</a>
                                                </label>
                                                <input type="file" id="files" name="files[]"
                                                    accept="image/jpg, image/jpeg, image/png"
                                                    style="visibility: hidden; position: absolute;" multiple />
                                            </div>
                                            <p id="files-area">
                                                <span id="filesList">
                                                    <span id="files-names"></span>
                                                </span>
                                                <span id="filesList2">
                                                    <span id="files-names2">
                                                        @if (count($images) > 0)
                                                            @foreach ($images as $img)
                                                                <span class="file-block"
                                                                    id="span_{{ $img->id }}">
                                                                    <img class="imageThumb"
                                                                        src="{{ url('/storage/' . $img->image_path) }}"
                                                                        alt="">
                                                                    <span class="file-delete"
                                                                        onclick='deleteImage(`{{ $img->id }}`)'><span>x
                                                                            hapus</span></span>
                                                                </span>
                                                            @endforeach

                                                        @endif
                                                        <span id="deleteList">
                                                            <span id="delete-names"></span>
                                                        </span>
                                                    </span>
                                                </span>
                                            </p>
                                            <p class="help-block text-danger float-right">Format JPG/PNG/JPEG ||
                                                Maksimal 2 MB
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info float-right"
                                        id="btn_save">Simpan</button>
                                    <a href="{{ route('anggota.index') }}" type="button"
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
        <script src="{{ asset('maps/leaflet2.js') }}"></script>
        <script src="{{ asset('maps/jquery-2.2.4.min.js') }}" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
            crossorigin="anonymous"></script>
        <script src="{{ asset('maps/leaflet-locationpicker.js') }}"></script>
        <script>
            $('#geoloc5').leafletLocationPicker({
                alwaysOpen: true,
                mapContainer: "#fixedMapCont"
            }).on('changeLocation', function(e) {
                $(this)
                    .siblings('#latitude').val(e.latlng.lat)
                    .siblings('#longitude').val(e.latlng.lng)
                    .siblings('i').text('"' + e.location + '"');
            });
            $('#form-edit').submit(function(e) {
                if ($('.imageThumb').length == 0 || ($('.imageThumb').length < 1)) {
                    alert('Minimal 1 gambar');
                    e.preventDefault();
                }

                if ($('.imageThumb').length > 10) {
                    alert('Maksimal 10 gambar');
                    e.preventDefault();
                }
            });

            if (window.File && window.FileList && window.FileReader) {
                const dt = new DataTransfer();
                var j = 0;

                $('#files').on('change', function(e) {
                    var files = e.target.files;

                    for (let file of this.files) {
                        dt.items.add(file);
                    }
                    this.files = dt.files;

                    for (var i = 0; i < files.length; i++) {
                        var f = files[i];
                        var r = new FileReader();
                        r.onload = (function(f) {
                            return function(e) {
                                if (f.size > 2000000) {
                                    alert(`Foto ${f.name} tidak boleh lebih dari 2Mb.`);

                                    for (let i = 0; i < dt.items.length; i++) {
                                        if (f.name === dt.items[i].getAsFile().name) {
                                            dt.items.remove(i);
                                            continue;
                                        }
                                    }

                                    document.getElementById('files').files = dt.files;
                                } else {
                                    /* append files */
                                    let fileBloc = $('<span/>', {
                                            class: 'file-block'
                                        }),
                                        fileName = $('<span/>', {
                                            class: 'name',
                                            text: f.name
                                        })
                                    fileImage = $('<img/>', {
                                        class: 'imageThumb',
                                        src: e.target.result
                                    });
                                    fileBloc.append(fileImage)
                                        .append(
                                            '<span class="file-delete"><span>x hapus</span></span>'
                                        )
                                        .append(fileName)
                                        .append('<input type="file" name="unit_images' +
                                            j +
                                            '" id="unit_images' + j +
                                            '" style="visibility: hidden; position: absolute;" />'
                                        );
                                    $("#filesList > #files-names").append(fileBloc);

                                    var tst = new DataTransfer();
                                    tst.items.add(f);
                                    document.getElementById('unit_images' + j).files = tst
                                        .files;

                                    /* delete files */
                                    $('span.file-delete').click(function() {
                                        let name = $(this).next('span.name')
                                            .text();
                                        $(this).parent().remove();
                                        for (let i = 0; i < dt.items
                                            .length; i++) {
                                            if (name === dt.items[i].getAsFile()
                                                .name) {
                                                dt.items.remove(i);
                                                continue;
                                            }
                                        }

                                        document.getElementById('files').files =
                                            dt
                                            .files;
                                    });

                                    j++;
                                }
                            };
                        })(f);

                        r.readAsDataURL(f);
                    };
                });
            } else {
                alert("Your browser doesn't support to File API")
            }

            let jmlDeleted = 0;

            function deleteImage(id) {
                let fileBloc = $('<span/>', {
                    class: 'file-block'
                });
                fileBloc.append("<input type='hidden' name='delete_images[]' value='" + id + "' >");
                $("#deleteList > #delete-names").append(fileBloc);
                $("#span_" + id).hide();
                jmlDeleted += 1;
            }

            $("#form-edit").submit(function(e) {
                e.preventDefault();
                var form = document.getElementById("form-edit");

                let postData = new FormData();
                $.each($('#form-edit :input').serializeObject(), function(x, y) {
                    postData.append(x, y);
                });

                if ($('#image_path')[0].files[0]) {
                    postData.append('image_path', $('#image_path')[0].files[0]);

                } else {
                    postData.append('image_path', 'undifined');
                }

                var files = $('#files')[0].files;
                for (var i = 0; i < files.length; i++) {
                    postData.append('files[]', files[i]);
                }

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
                        window.location.href = "{{ route('anggota.index') }}";

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
                $('.select2-kota').select2();
                $('.select2-kecamatan').select2();
                $('.select2-desa').select2();
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

                loadKota($('#kota_id'), "{{ $data->provinsi_id }}", "{{ $data->kota_id }}");

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

                $('.select2-kota').on('change', async function() {
                    $.when(loadKecamatan($('#kecamatan_id'), $(this).val(), )).done(function(
                        kecamatan) {});
                });

                loadKecamatan($('#kecamatan_id'), "{{ $data->kota_id }}", "{{ $data->kecamatan_id }}");

                function loadKecamatan(target, kota_id, selected_id = null) {
                    let option = "<option value=\"\">-- Pilih Kecamatan --</option>";

                    if (!kota_id) {
                        target.html(option);
                        return
                    }

                    return ajax({
                        type: "GET",
                        url: "{{ route('kecamatan.option') }}?kota_id=" + kota_id,
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

                $('.select2-kecamatan').on('change', async function() {
                    $.when(loadDesa($('#desa_id'), $(this).val(), )).done(function(
                        desa) {});
                });

                loadDesa($('#desa_id'), "{{ $data->kecamatan_id }}", "{{ $data->desa_id }}");

                function loadDesa(target, kecamatan_id, selected_id = null) {
                    let option = "<option value=\"\">-- Pilih Desa --</option>";

                    if (!kecamatan_id) {
                        target.html(option);
                        return
                    }

                    return ajax({
                        type: "GET",
                        url: "{{ route('desa.option') }}?kecamatan_id=" + kecamatan_id,
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
