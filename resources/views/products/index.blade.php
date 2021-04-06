@extends('layouts.master')
@section('content')
      <div class="container mt-5 content">
          <!-- user list -->
         <div class="row">
            <div class="col-lg-12 margin-tb">
               <div class="pull-left">
                    <h2><i class="fab fa-product-hunt"></i>&nbsp;Products List</h2>
               </div>
               <div class="mb-3" style="float: right;">
                  <a class="btn btn-info" onClick="addProduct()" href="javascript:void(0)" title="Create New Product"><i class="fab fa-product-hunt"></i>&nbsp;New</a>
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
      <div class="modal fade" id="product-modal" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="productModal"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form action="javascript:void(0)" id="productForm" name="productForm" class="form-horizontal" method="POST">
                     <input type="hidden" name="id" id="id">
                     <div class="form-group">
                     <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                           <input type="text" class="form-control" id="name" name="name"  maxlength="50" required="">
                        </div>
                     </div>
                     <div class="form-group">
                     <label for="email" class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-12">
                           <input type="text" class="form-control" id="price" name="price" required="">
                        </div>
                     </div>
                     <div class="form-group">
                     <label for="email" class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-12">
                           <textarea class="form-control" id="description" name="description" required=""></textarea>
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

    function addProduct(){
        $('#productForm').trigger("reset");
        $('#productModal').html("<i class='fab fa-product-hunt'></i>&nbsp;New Product");
        $('#product-modal').modal('show');
        $('#id').val('');
    }

    function editFunc(id){
        $.ajax({
            type:"POST",
            url: "{{ route('products.edit') }}",
            data: {'id':id},
            dataType: 'json',
            success: function(res){
                $('#productModal').html("<i class='fab fa-product-hunt'></i>&nbsp;Edit Product");
                $('#product-modal').modal('show');
                $('#id').val(res.id);
                $('#name').val(res.name);
                $('#description').val(res.description);
                $('#price').val(res.price);
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
                url: "{{ route('products.delete') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){
                    if(res.success) {
                        swal('Success', 'Operation Successfully completed', 'success');
                        var oTable = $('#list-products').dataTable();
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
        }else if($('#description').val() == "" || $('#description').val() == undefined || $('#description').val() == null) {
         swal('Error', "Please enter description", 'error');
         return false;
        }else if($('#price').val() == "" || $('#price').val() == undefined || $('#price').val() == null) {
         swal('Error', "Please enter price", 'error');
         return false;
        }else{
            return true;
        }
    }

    $('#productForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if(validateForm()){
            swal({
                title: "Are you sure?",
                text: "Your want to save the product details",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, do it!",
                closeOnConfirm: false
            }).then(function() {
                $.ajax({
                    type:'POST',
                    url: "{{ route('products.store')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                    if(res.success) {
                            swal('Success', 'Operation Successfully completed', 'success');
                            $("#product-modal").modal('hide');
                            var oTable = $('#list-products').dataTable();
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
