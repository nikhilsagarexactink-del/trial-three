@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $product)
        <div class="col-md-4">
            <div class="card">
                <!-- Badge (Available/Disabled) -->
                <div class="badge-container">
                    @if ($product->availability_status == 'available')
                        <span class="badge-available">Available</span>
                    @else
                        <span class="badge-disabled">Unavailable</span>
                    @endif
                </div>

                <!-- Product Image -->
                <a href="#">
                    @if (!empty($product->image) && !empty($product->image->media->base_url))
                        <img class="card-img-top" src="{{ $product->image->media->base_url }}" alt="{{ $product->title }}">
                    @else
                        <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}"
                            alt="{{ $product->title }}">
                    @endif
                </a>

                <!-- Product Info -->
                <div class="card-body ">
                    <h5 class="card-title" data-toggle="tooltip" data-placement="top"
                        title="{{ ucfirst($product->title) }}">
                        {{ ucfirst($product->title) }}
                    </h5>
                    <span data-toggle="tooltip" data-placement="top"
                        title="{{ ucfirst($product->description) }}">{{ ucfirst(substr($product->description, 0, 100)) }}
                        {{ strlen($product->description) > 100 ? '...' : '' }}
                    </span>
                    <div class="product-details">
                        <span class="product-date">Point Cost: {{ ucfirst($product->point_cost) }} </span>
                    </div>
                    <!-- Add to Cart Button -->
                    <div class=" product-card-cta">
                        @if ($product->carts != null)
                            <!-- Check if there is a cart entry -->
                            <!-- Product is already in the cart -->
                            <a href="{{ route('user.cartIndex', ['user_type' => $userType]) }}"
                                class="btn btn-primary btn-outline">Go To Cart</a>
                        @else
                            <!-- Product is not in cart -->
                            <button type="button" class="btn btn-primary btn-outline"
                                onClick="addToCart('{{ $product->id }}')">Add To Cart</button>
                        @endif
                        <button type="button" class="btn btn-secondary ripple-effect-dark"
                            onClick="buyNow('{{ $product->id }}','{{ $product->point_cost }}')"
                            {{ $product->availability_status == 'unavailable' || $product->availability_status == null ? 'disabled' : '' }}>Buy
                            Now</button>
                    </div>
                </div>
            </div>
        </div>

        @php $i++; @endphp
    @endforeach
@else
    <div class="alert alert-danger" role="alert">
        Oops. No Product Found. Try again!
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadList(pageLink);
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
