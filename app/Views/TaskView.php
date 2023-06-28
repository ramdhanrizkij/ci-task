<?= $this->extend('partials/main') ?>

<?= $this->section('content') ?>
<?= $this->include('component/modals')?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"> CI 4 - Aplikasi Task Management</h3>
                <div class="float-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-block btn-primary" id="create-task"
                            data-toggle="modal" data-target="#modal-create-task"><i class="fa fa-plus"></i> Buat
                            Task</button>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="data-task" class="table table-striped dataTable">
                                    <thead>
                                        <tr role="row">
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>


<?= $this->section('extra-js') ?>
<script>
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token_name"]').attr('content')
            }
        });

        function changeStatus(id){
            console.log("Change ID : ", id);
        }

        var dataTableTask = $('#data-task').DataTable({
            autoWidth: false,
            serverSide: true,
            processing: true,
            order: [
                [1, 'asc']
            ],
            columnDefs: [{
                orderable: false,
                targets: [0, 3]
            }],

            ajax: {
                url: "<?= route_to('datatable') ?>",
                method: 'POST'
            },

            columns: [
                {
                    "data": null
                },
                {
                    "data": "judul"
                },
                {
                    "data": function (data) {
                        let status = `<div style="display:flex;align-items:center"><input type='checkbox'
                            ${data.status==1?'checked':''}
                            style="width:20px;height:20px" class="status" data-id="${data.id}"> &nbsp;`;
                        switch (data.status) {
                            case '0':
                                return status+`<span class="right badge badge-danger span-status-${data.id}">Not Complete</span></div>`
                                break;
                            case '1':
                                return status+`<span class="right badge badge-success span-status-${data.id}">Complete</span></div>`
                                break;
                        }
                    }
                },
                {
                    "data": function (data) {
                        return `<td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary btn-edit" data-id="${data.id}"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-danger btn-delete" data-id="${data.id}"><i class="fas fa-trash"></i></button>
                            </div>
                            </td>`
                    }
                }
            ]
        });

        dataTableTask.on('draw.dt', function () {
            var PageInfo = $('#data-task').DataTable().page.info();
            dataTableTask.column(0, {
                page: 'current'
            }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        $(document).on('change', '.status', function () {
            var status = $(this).attr('data-id')
            console.log("Change status", status)
            var obj = $(`.span-status-${status}`)
            if(obj.text()=='Not Complete'){
                obj.text('Complete')
                obj.removeClass( "badge-danger").addClass('badge-success')
            }else {
                obj.text('Not Complete')
                obj.removeClass( "badge-success").addClass('badge-danger')
            }
            // Send ajax to change status
            $.ajax({
                url: `<?=base_url('task')?>/${status}/update-status`,
                method: 'GET',
            }).done((response) => {
                var editForm = $('#form-edit-task');
                Toast.fire({
                    icon: 'success',
                    title: "Status diupdate"
                })
            }).fail((error) => {
                Toast.fire({
                    icon: 'error',
                    title: error.responseJSON.messages.error,
                });
            })

        })

        
        $(document).on('click', '#btn-save-task', function () {
            $('.text-danger').remove();
            $('.is-invalid').removeClass('is-invalid');
            var createForm = $('#form-create-task');

            $.ajax({
                url: '<?=base_url('/task')?>',
                method: 'POST',
                data: createForm.serialize()
            }).done((data) => {
                Toast.fire({
                    icon: 'success',
                    title: "Success"
                })
                dataTableTask.ajax.reload();
                $("#form-create-task").trigger("reset");
                $("#modal-create-task").modal('hide');

            }).fail((xhr, status, error) => {
                console.log("Error : ", xhr)
                console.log("Error status : ", status)
                console.log("Error data ; ", error)
                // if (xhr.responseJSON.message) {
                //     Toast.fire({
                //         icon: 'error',
                //         title: xhr.responseJSON.message,
                //     });
                // }

                $.each(xhr.responseJSON.messages, (elem, messages) => {
                    createForm.find('input[name="' + elem + '"]').addClass('is-invalid')
                        .after('<p class="text-danger">' + messages + '</p>');
                });
            })
        })

        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault();
            $.ajax({
                url: `<?= base_url('/task') ?>/${$(this).attr('data-id')}`,
                method: 'GET',
            }).done((resp) => {
                var editForm = $('#form-edit-task');
                console.log(resp.data)
                editForm.find('input[name="judul"]').val(resp.data.judul);
                $("#task_id").val(resp.data.id);
                $("#modal-edit-task").modal('show');
            }).fail((error) => {
                Toast.fire({
                    icon: 'error',
                    title: error.responseJSON.messages.error,
                });
            })
        });

        $(document).on('click', '#btn-update-task', function (e) {
            e.preventDefault();
            $('.text-danger').remove();
            var editForm = $('#form-edit-task');

            $.ajax({
                url: `<?= base_url('task') ?>/${$('#task_id').val()}`,
                method: 'PUT',
                data: editForm.serialize()

            }).done((data, textStatus) => {
                Toast.fire({
                    icon: 'success',
                    title: textStatus
                })
                dataTableTask.ajax.reload();
                $("#form-edit-task").trigger("reset");
                $("#modal-edit-task").modal('hide');

            }).fail((xhr, status, error) => {
                $.each(xhr.responseJSON.messages, (elem, messages) => {
                    editForm.find('input[name="' + elem + '"]').addClass('is-invalid')
                        .after('<p class="text-danger">' + messages + '</p>');
                });
            })
        });

        $(document).on('click', '.btn-delete', function (e) {
            Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `<?= base_url('/task') ?>/${$(this).attr('data-id')}`,
                            method: 'DELETE',
                        }).done((data, textStatus) => {
                            Toast.fire({
                                icon: 'success',
                                title: textStatus,
                            });
                            dataTableTask.ajax.reload();
                        }).fail((error) => {
                            Toast.fire({
                                icon: 'error',
                                title: error.responseJSON.messages.error,
                            });
                        })
                    }
                })
        });

        $('#modal-create-task').on('hidden.bs.modal', function () {
            $(this).find('#form-create-task')[0].reset();
            $('.text-danger').remove();
            $('.is-invalid').removeClass('is-invalid');
        });

        $('#modal-edit-task').on('hidden.bs.modal', function () {
            $(this).find('#form-edit-permission')[0].reset();
            $('.text-danger').remove();
            $('.is-invalid').removeClass('is-invalid');
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    });
</script>
<?= $this->endSection() ?>