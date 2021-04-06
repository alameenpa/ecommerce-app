@extends('layouts.master')
@section('content')
      <div class="container mt-5 content">
          <!-- user list -->
         <div class="row">
            <div class="col-lg-12 margin-tb">
               <div class="pull-left">
                    <h2><i class="fas fa-truck"></i>&nbsp;Orders List</h2>
               </div>
               <div class="mb-3" style="float: right;">
                  <a class="btn btn-info" onClick="addOrder()" href="javascript:void(0)" title="Create New Order"><i class="fas fa-truck"></i>&nbsp;New</a>
                  <a class="btn btn-warning" href="javascript:void(0)" onclick="backwardNavigation()" title="Back to Dashboard"><i class="fas fa-arrow-circle-left"></i></a>
               </div>
            </div>
            <div class="card-body">
            {!! $dataTable->table() !!}
            {!! $dataTable->scripts() !!}
            </div>
         </div>
      </div>

      <!-- boostrap model -->
      <div class="modal fade" id="transaction-modal" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="transactionModal"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body transaction-details">
               </div>
               <div class="modal-footer">
               </div>
            </div>
         </div>
      </div>

      <div class="modal fade" id="order-modal" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="orderModal"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form action="javascript:void(0)" id="orderForm" name="orderForm" class="form-horizontal" method="POST">
                     <input type="hidden" name="id" id="id">
                     <div class="form-group col-sm-12">
                            <div class="items-list">
                                <input type="hidden" id="order_hidden_input" name="order_hidden_input" required=""/>
                                <table class="table">
                                    <thead>
                                        <th>Product</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Action</th>
                                    </thead>
                                    <tbody class="items-list-body">
                                    </tbody>
                                    <tbody>
                                        <tr>
                                            <td>
                                            <select id="product" name="product" class="form-control" onchange="showPrice()">
                                                    <option value="null">-- Select any Product --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{$product->id}}" data-price="{{$product->price}}">{{$product->name}}</option>
                                                    @endforeach
                                            </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" onkeyup="showPrice()" class="form-control" id="quantity" name="quantity" placeholder="Quantity here">
                                            </td>
                                            <td class="text-right price-input p-3" style="color: red;">&nbsp;</td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-sm btn-success" onclick="addItems()" id="add-items"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <textarea id="address" placeholder="Address to communicate" name="address" class="form-control"></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                <table>
                            </div>
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
               <div class="col-sm-offset-2 col-sm-12">
                        <button type="submit" class="btn btn-primary" id="btn-save">Save</button>
                        <button type="reset" class="btn btn-warning" id="btn-save">Reset</button>
                     </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end bootstrap model -->

      <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
   <script type="text/javascript">
   $(document).ready( function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function showPrice() {
        $('.price-input').text('');
        let product_id = $('#product').val();
        let quantity = $('#quantity').val();

        if(product_id != "" && product_id != null && quantity != "" && quantity != null) {
            let product_price = parseFloat($('#product option:selected').attr('data-price'));
            quantity = parseFloat(quantity);
            $('.price-input').text(product_price*quantity);
        }
    }

    function addItems() {
        let product = $('#product').val();
        let product_text = $('#product option:selected').text();
        let product_price = $('#product option:selected').attr('data-price');
        let quantity = $('#quantity').val();

        if(product == "" || product == "null" || product == null) {
            swal("Error","Please choose any product", "error");
            return false;
        }else if(quantity == "" || quantity == null) {
            swal("Error","Please enter quantity", "error");
            return false;
        }

        let hiddenArray = [];
        let ordersInput = $('#order_hidden_input').val();
        if(ordersInput != "" && ordersInput != null) {
            hiddenArray = JSON.parse($('#order_hidden_input').val());
        }

        let existing_val = false;
        if(hiddenArray.length > 0) {
            $.each(hiddenArray, function(index,val){
                let prdId = parseInt(val.product_id);
                let selectedProduct = parseInt(product);
                if(selectedProduct == prdId) {
                    quantity = (parseFloat(quantity) + parseFloat(val.quantity));
                    hiddenArray[index].quantity = quantity;
                    existing_val = true;
                }
            });
        }

        if(!existing_val) {
            hiddenArray.push({"product_id":product, "product_name":product_text, "product_price":product_price, "quantity":quantity});
        }

        $('#order_hidden_input').val("");
        if(hiddenArray.length > 0) {
            let stringifiedData = JSON.stringify(hiddenArray);
            $('#order_hidden_input').val(stringifiedData);
        }

        let html = "";
        if(hiddenArray.length > 0) {
            let total = 0;
            $.each(hiddenArray, function(index,val){
                let total_val=(parseFloat(val.quantity)*parseFloat(val.product_price));
                total+=total_val;
                html+="<tr class='item_"+index+"'><td>"+val.product_name+"</td><td class='text-center'>"+val.quantity+"</td><td class='text-right' style='color:red'>"+(val.quantity*val.product_price)+"</td><td class='text-right'><button type='button' class='btn btn-sm btn-danger' onclick='removeItems("+index+")' id='remove-items'><i class='fa fa-minus'></i></button></td></tr>";
            });
            html+="<tr><td>&nbsp;</td><td class='text-center'>&nbsp;</td><td class='text-right' style='color:red'>Total: "+total+"<input type='hidden' id='total' name='total' value='"+total+"'/></td><td class='text-right'>&nbsp;</td></tr>";
        }
        $('.items-list-body').html(html);
        $('#product').val('');
        $('#quantity').val('');
        $('.price-input').text('');
    }

    function removeItems(itemIndex) {
        let hiddenArray = [];
        let tempArray = [];
        let ordersInput = $('#order_hidden_input').val();
        if(ordersInput != "" && ordersInput != null) {
            hiddenArray = JSON.parse($('#order_hidden_input').val());
        }

        if(hiddenArray.length > 0) {
            $.each(hiddenArray, function(index,val){
                console.log(itemIndex, index);
                if(itemIndex != index) {
                    tempArray.push(val);
                }
            });
        }

        let html = "";
        $('#order_hidden_input').val("");
        if(tempArray.length>0) {
            let stringifiedData = JSON.stringify(tempArray);
            $('#order_hidden_input').val(stringifiedData);

            let total = 0;
            $.each(tempArray, function(index,val){
                let total_val=(parseFloat(val.quantity)*parseFloat(val.product_price));
                total+=total_val;
                html+="<tr class='item_"+index+"'><td>"+val.product_name+"</td><td class='text-center'>"+val.quantity+"</td><td class='text-right' style='color:red'>"+(val.quantity*val.product_price)+"</td><td class='text-right'><button type='button' class='btn btn-sm btn-danger' onclick='removeItems("+index+")' id='remove-items'><i class='fa fa-minus'></i></button></td></tr>";
            });
            html+="<tr><td>&nbsp;</td><td class='text-center'>&nbsp;</td><td class='text-right' style='color:red'>Total: "+total+"<input type='hidden' id='total' name='total' value='"+total+"'/></td><td class='text-right'>&nbsp;</td></tr>";
        }
        $('.items-list-body').html(html);
    }

    function addOrder(){
        $('.items-list-body').html('');
        $('#order_hidden_input').val('');
        $('#orderForm').trigger("reset");
        $('#orderModal').html("<i class='fas fa-truck'></i>&nbsp;New Order");
        $('#order-modal').modal('show');
        $('#id').val('');
    }

    function editFunc(id){
        let hiddenArray = [];
        $.ajax({
            type:"POST",
            url: "{{ route('orders.edit') }}",
            data: {'id':id},
            dataType: 'json',
            success: function(res){
                $('#orderModal').html("<i class='fas fa-truck'></i>&nbsp;Edit Order");
                $('#order-modal').modal('show');
                $('#id').val(res.id);
                $('#address').val(res.address);

                let html = "";
                $.each(res.transactions, function(index, value){
                    html+="<tr class='item_"+index+"'><td>"+value.product.name+"</td><td class='text-center'>"+value.quantity+"</td><td class='text-right' style='color:red'>"+(value.amount)+"</td><td class='text-right'><button type='button' class='btn btn-sm btn-danger' onclick='removeItems("+index+")' id='remove-items'><i class='fa fa-minus'></i></button></td></tr>";
                    hiddenArray.push({"product_id":value.product_id, "product_name":value.product.name, "product_price":value.product.price, "quantity":value.quantity});
                });
                html+="<tr><td>&nbsp;</td><td class='text-center'>&nbsp;</td><td class='text-right' style='color:red'>Total: "+res.amount+"<input type='hidden' id='total' name='total' value='"+res.amount+"'/></td><td class='text-right'>&nbsp;</td></tr>";

                $('#order_hidden_input').val("");
                if(hiddenArray.length>0) {
                    let stringifiedData = JSON.stringify(hiddenArray);
                    $('#order_hidden_input').val(stringifiedData);
                }
                $('.items-list-body').html(html);
            }
        });
    }

    function viewOrder(id) {
        $.ajax({
            type:"POST",
            url: "{{ route('transactions.index') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
                if(res.transactions) {
                    $('#transactionModal').html("<i class='fas fa-people-carry'></i>&nbsp;Transactions Details");
                    $('#transaction-modal').modal('show');
                    let html ="<table class='table table-striped transaction-table'><thead><th>Product</th><th>Quantity</th><th>Price</th><th>Status</th><th>Last Updated</th></thead><tbody id='transaction-tbl-body'>";
                    $.each(res.transactions, function(ind, val){
                        html +="<tr class='transaction-item-"+val.id+"'><td>"+val.product.name+"</td><td class='text-center'>"+val.quantity+"</td><td class='text-center'>"+val.amount+"</td><td><select class='form-control' id='transaction_status_"+val.id+"' onchange='changeStatus("+val.id+")' "+((val.status == '0')? 'disabled':'')+"><option value='0' "+((val.status == '0') ? 'selected':(val.status != '1')? 'disabled':'')+">Cancelled</option><option value='1' "+((val.status == 1) ? 'selected':((val.status > 1)? 'disabled':''))+">Received</option><option value='2' "+((val.status == 2) ? 'selected':((val.status > 2)? 'disabled':''))+">Confirmed</option><option value='3' "+((val.status == 3) ? 'selected':((val.status > 3)? 'disabled':''))+">Dispatched</option><option value='4' "+((val.status == 4) ? 'selected':((val.status > 4)? 'disabled':''))+">Delivered</option></select></td><td>"+moment(val.updated_at).format('MMM DD, YYYY HH:mm A')+"</td></tr>";
                    });
                    html +="</tbody></table>";
                    $('.transaction-details').html(html);
                    $('.transaction-table').dataTable();
                }
            }
        });
    }

    function changeStatus(id) {
        let transaction_status = $('#transaction_status_'+id).val();
        $.ajax({
            type:"POST",
            url: "{{ route('transactions.status') }}",
            data: { id: id, transaction_status:transaction_status },
            dataType: 'json',
            success: function(res){
                let val = res.transaction;
                if(val) {
                    $('.transaction-item-'+id).remove();
                    let html ="<tr class='transaction-item-"+val.id+"'><td>"+val.product.name+"</td><td>"+val.quantity+"</td><td>"+val.amount+"</td><td><select class='form-control' id='transaction_status_"+val.id+"' onchange='changeStatus("+val.id+")' "+((val.status == '0')? 'disabled':'')+"><option value='0' "+((val.status == '0') ? 'selected':(val.status != '1')? 'disabled':'')+">Cancelled</option><option value='1' "+((val.status == 1) ? 'selected':((val.status > 1)? 'disabled':''))+">Received</option><option value='2' "+((val.status == 2) ? 'selected':((val.status > 2)? 'disabled':''))+">Confirmed</option><option value='3' "+((val.status == 3) ? 'selected':((val.status > 3)? 'disabled':''))+">Dispatched</option><option value='4' "+((val.status == 4) ? 'selected':((val.status > 4)? 'disabled':''))+">Delivered</option></select></td><td>"+moment(val.updated_at).format('MMM DD, YYYY HH:mm A')+"</td></tr>";
                    $('#transaction-tbl-body').append(html);
                    var oTable = $('#list-orders').dataTable();
                    oTable.fnDraw(false);
                    swal("Success", "Status changed successfully", "success");
                }
            }
        });
    }

    function cancelOrder(id){
        var id = id;
        swal({
            title: "Are you sure?",
            text: "Your will not be able to undo this operation!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, cancel it!",
            closeOnConfirm: false
        }).then(function() {
            $.ajax({
                type:"POST",
                url: "{{ route('orders.cancel') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){
                    if(res.success) {
                        swal('Success', 'Operation Successfully completed', 'success');
                        var oTable = $('#list-orders').dataTable();
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
       if($('#order_hidden_input').val() == "" || $('#order_hidden_input').val() == undefined || $('#order_hidden_input').val() == null) {
         swal('Error', "Please add any product to order", 'error');
         return false;
        }else if($('#address').val() == "" || $('#address').val() == undefined || $('#address').val() == null) {
         swal('Error', "Please enter Address", 'error');
         return false;
        }else{
            return true;
        }
    }

    $('#orderForm').submit(function(e) {
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
                    url: "{{ route('orders.store')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                    if(res.success) {
                            swal('Success', 'Operation Successfully completed', 'success');
                            $("#order-modal").modal('hide');
                            var oTable = $('#list-orders').dataTable();
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
