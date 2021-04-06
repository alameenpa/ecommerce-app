@extends('layouts.master')
@section('content')
      <div class="container mt-5 content">
          <!-- user list -->
         <div class="row">
            <div class="col-lg-12 margin-tb">
               <div class="pull-left">
                    <h2><i class="fa fa-users" aria-hidden="true"></i>&nbsp;Users List</h2>
               </div>
               <div class="mb-3" style="float: right;">
                  <a class="btn btn-info" onClick="addUser()" href="javascript:void(0)" title="Create New User"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;New</a>
                  <a class="btn btn-warning" href="javascript:void(0)" onclick="backwardNavigation()" title="Back to Dashboard"><i class="fas fa-arrow-circle-left"></i></a>
               </div>
            </div>
            <div class="card-body">
            {!! $dataTable->table() !!}
            {!! $dataTable->scripts() !!}
            </div>
         </div>
      </div>

      <!-- boostrap user model -->
      <div class="modal fade" id="user-modal" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="userModal"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form action="javascript:void(0)" id="userForm" name="userForm" class="form-horizontal" method="POST">
                     <input type="hidden" name="id" id="id">
                     <div class="form-group">
                     <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                           <input type="text" class="form-control" id="name" name="name"  maxlength="50" required="">
                        </div>
                     </div>
                     <div class="form-group">
                     <label for="email" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-12">
                           <input type="email" class="form-control" id="email" name="email" required="">
                        </div>
                     </div>
                     <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="btn-save">Save</button>
                        <button type="reset" class="btn btn-warning" id="btn-save">Reset</button>
                     </div>
                  </form>
               </div>
               <div class="modal-footer"></div>
            </div>
         </div>
      </div>
      <!-- end bootstrap model -->
   <script type="text/javascript">
   $(document).ready( function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function addUser(){
        $('#userForm').trigger("reset");
        $('#userModal').html("<i class='fa fa-user' aria-hidden='true'></i>&nbsp;New User");
        $('#user-modal').modal('show');
        $('#id').val('');
    }

    function editFunc(id){
        $.ajax({
            type:"POST",
            url: "{{ route('users.edit') }}",
            data: {'id':id},
            dataType: 'json',
            success: function(res){
                $('#userModal').html("<i class='fa fa-user' aria-hidden='true'></i>&nbsp;Edit User");
                $('#user-modal').modal('show');
                $('#id').val(res.id);
                $('#name').val(res.name);
                $('#email').val(res.email);
            }
        });
    }

    function deleteFunc(id){
        var id = id;
        swal({
            title: "Are you sure?",
            text: "Your will not be able to undo this operation!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }).then(function() {
            $.ajax({
                type:"POST",
                url: "{{ route('users.delete') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){
                    if(res.success) {
                        swal('Success', 'Operation Successfully completed', 'success');
                        var oTable = $('#list-users').dataTable();
                        oTable.fnDraw(false);
                    }else{
                        swal('Error', res.message, 'error');
                    }
                }
            });
        }).catch(swal.noop);
    }

    function validateForm() {
       //validate input values
       if($('#name').val() == "" || $('#name').val() == undefined || $('#name').val() == null) {
         swal('Error', "Please enter a name", 'error');
         return false;
        }else if($('#email').val() == "" || $('#email').val() == undefined || $('#email').val() == null) {
         swal('Error', "Please enter email", 'error');
         return false;
        }else{
            return true;
        }
    }

    $('#userForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if(validateForm()){
            swal({
                title: "Are you sure?",
                text: "Your want to save the user details",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, do it!",
                closeOnConfirm: false
            }).then(function() {
                $.ajax({
                    type:'POST',
                    url: "{{ route('users.store')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                    if(res.success) {
                            swal('Success', 'Operation Successfully completed', 'success');
                            $("#user-modal").modal('hide');
                            var oTable = $('#list-users').dataTable();
                            oTable.fnDraw(false);
                            $("#btn-save").html('Submit');
                            $("#btn-save"). attr("disabled", false);
                        }else{
                            swal('Error', res.message, 'error');
                        }
                    }
                });
            }).catch(swal.noop);
        }
    });
   </script>
@endsection
