@extends('layout.app')

@section('title', 'Data Subkategori')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h4 class="card-title">
            Data Subkategori
        </h4>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-end mb-4">
            <a href="#modal-form" class="btn btn-primary modal-tambah">Tambah Data</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Subkategori</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-form" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Subkategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-subkategori">
                            <div class="form-group">
                                <label for="">Nama Subkategori</label>
                                <input type="text" class="form-control" name="nama_subkategori"
                                    placeholder="Nama Subkategori" required>
                            </div>
                            <div class="form-group">
                                <label for="">Kategori</label>
                                <select name="id_kategori" id="id_kategori" class="form-control">
                                    @foreach ($categories as $category)
                                    <option value="{{$category->id}}">{{$category->nama_kategori}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Deskripsi</label>
                                <textarea name="deskripsi" placeholder="Deskripsi" class="form-control" id="" cols="30"
                                    rows="10" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Gambar</label>
                                <input type="file" class="form-control" name="gambar">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        $.ajax({
            url: '/api/subcategories',
            success: function({
                data
            }) {

                let row;
                data.map(function(val, index) {
    row += `
        <tr>
            <td>${index+1}</td>
            <td>${val.nama_subkategori}</td>
            <td>${val.category ? val.category.nama_kategori : 'Nama Kategori Tidak Tersedia'}</td>
            <td>${val.deskripsi}</td>
            <td><img src="/uploads/${val.gambar}" width="150"></td>
            <td>
                <a href="#modal-form" data-id="${val.id}" class="btn btn-warning modal-ubah">Edit</a>
                <a href="#" data-id="${val.id}" class="btn btn-danger btn-hapus">hapus</a>
            </td>
        </tr>
        `;
});

                $('tbody').append(row)
            }
        });

        $(document).on('click', '.btn-hapus', function() {
            const id = $(this).data('id');
            const token = localStorage.getItem('token');

            // Tampilkan konfirmasi penghapusan yang menarik menggunakan SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda tidak dapat mengembalikan data yang sudah dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lanjutkan dengan proses penghapusan jika dikonfirmasi
                    $.ajax({
                        url: '/api/subcategories/' + id,
                        type: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        success: function(data) {
                            if (data.message == 'success') {
                                // Tampilkan notifikasi bahwa data berhasil dihapus
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                    ).then(() => {
                                location.reload();
                            });
                            }
                        }
                    });
                }
            });
        });

        $('.modal-tambah').click(function() {
            $('#modal-form').modal('show');
            $('input[name="nama_subkategori"]').val('');
            $('textarea[name="deskripsi"]').val('');
            $('select[name="id_kategori"]').val('');

            $('.form-subkategori').submit(function(e) {
                e.preventDefault();
                const token = localStorage.getItem('token');
                const frmdata = new FormData(this);

                $.ajax({
                    url: 'api/subcategories',
                    type: 'POST',
                    data: frmdata,
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        "Authorization": 'Bearer ' + token
                    },
                    success: function(data) {
                        if (data.success) {
                            // Tampilkan SweetAlert setelah tambah berhasil
                            Swal.fire(
                                'Berhasil!',
                                'Data berhasil ditambah.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            });
        });

        // Penanganan acara untuk tombol edit
        $(document).on('click', '.modal-ubah', function() {
            $('#modal-form').modal('show');
            const id = $(this).data('id');

            $.get('/api/subcategories/' + id, function({
                data
            }) {
                $('input[name="nama_subkategori"]').val(data.nama_subkategori);
                $('select[name="id_kategori"]').val(data.id_kategori);
                $('textarea[name="deskripsi"]').val(data.deskripsi);
            });

            $('.form-subkategori').submit(function(e) {
                e.preventDefault();
                const token = localStorage.getItem('token');
                const frmdata = new FormData(this);

                $.ajax({
                    url: `api/subcategories/${id}?_method=PUT`,
                    type: 'POST',
                    data: frmdata,
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        "Authorization": 'Bearer ' + token
                    },
                    success: function(data) {
                        if (data.success) {
                            // Tampilkan SweetAlert setelah edit berhasil
                            Swal.fire(
                                'Berhasil!',
                                'Data berhasil diubah.',
                                'success'
                            ).then(() => {
                               location.reload();
                            });
                        }
                    }
                });
            });
        });

    });
</script>
@endpush
