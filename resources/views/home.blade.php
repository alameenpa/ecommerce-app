@extends('layouts.app')

@section('content')
<div class="container">
    <!--Section: Block Content-->
<section>

<!--Grid row-->
<div class="row">

  <!--Grid column-->
  <div class="col-lg-4 col-md-12 mb-4">

    <!-- Card -->
    <div class="card" id="users">

      <div class="card-body">
        <a href="{{route('users.index')}}"  style="text-decoration: none;">
          <p class="text-uppercase small mb-2"><strong>Users</strong></p>
          <h5 class="font-weight-bold mb-0">
              <span class="badge badge-pill badge-danger ml-2">{{$users}}</span>
          </h5>
        </a>
      </div>

    </div>
    <!-- Card -->

  </div>
  <!--Grid column-->

  <!--Grid column-->
  <div class="col-lg-4 col-md-6 mb-4">

    <!-- Card -->
    <div class="card products">

      <div class="card-body">
        <a href="{{route('products.index')}}" style="text-decoration: none;">
          <p class="text-uppercase small mb-2"><strong>Products</strong></p>
          <h5 class="font-weight-bold mb-0">
              <span class="badge badge-pill badge-primary ml-2">{{$products}}</span>
          </h5>
        </a>
      </div>

    </div>
    <!-- Card -->

  </div>
  <!--Grid column-->

  <!--Grid column-->
  <div class="col-lg-4 col-md-6 mb-4">

    <!-- Card -->
    <div class="card orders">

      <div class="card-body">
        <a href="{{route('orders.index')}}"  style="text-decoration: none;">
          <p class="text-uppercase small mb-2"><strong>Orders</strong></p>
          <h5 class="font-weight-bold mb-0">
              <span class="badge badge-pill badge-success ml-2">{{$orders}}</span>
          </h5>
        </a>
      </div>

    </div>
    <!-- Card -->

  </div>
  <!--Grid column-->

</div>
<!--Grid row-->

</section>
<!--Section: Block Content-->
</div>
@endsection
