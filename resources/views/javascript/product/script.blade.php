<script type="text/javascript">
    $("form").submit(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda Yakin Untuk Simpan Data?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-secondary mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertProcess();
                $('form').unbind('submit').submit();
            }
        })
    });

    function dataTable() {
        const url = $('#url_dt').val();
        $('#dt-product').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                error: function(xhr, error, code) {
                    sweetAlertError(xhr.statusText);
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    width: '5%',
                    searchable: false
                },
                {
                    data: 'picture',
                    defaultContent: '-',
                },
                {
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'category',
                    defaultContent: '-',
                },
                {
                    data: 'status',
                    width: '5%',
                    defaultContent: '-',
                    class: 'text-center'
                },
                {
                    data: 'sell_price',
                    defaultContent: '-',
                },
                {
                    data: 'discount_price',
                    defaultContent: '-',
                },
                {
                    data: 'created_at',
                    width: '10%',
                    defaultContent: '-',
                },
                {
                    data: 'updated_at',
                    width: '10%',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '5%',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function destroyRecord(id) {
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Apakah Anda Yakin Untuk Hapus Data?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-secondary mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertProcess();
                $.ajax({
                    url: '{{ url('product') }}/' + id,
                    type: 'DELETE',
                    cache: false,
                    data: {
                        _token: token
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(xhr, error, code) {
                        sweetAlertError(error);
                    }
                });
            }
        })
    }
</script>
