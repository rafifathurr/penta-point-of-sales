<div class="row g-4 justify-content-start mt-5">
    @foreach ($products as $product)
        <div class="col-md-6 col-lg-6 col-xl-6 py-3 d-flex">
            <div class="rounded position-relative shadow d-flex flex-column w-100">
                <div class="w-100 rounded-top border-bottom border-bottom-secondary bg-image"
                    style="background-image:url('{{ asset($product->picture) }}'); height:300px; background-size:cover; background-position:center;">
                </div>
                <div class="position-absolute" style="top: 10px; right: 10px;">
                    <div class="p-1 bg-primary text-white px-2 py-1 mr-1 rounded shadow">
                        <b>{{ $product->categoryProduct->name ?? '-' }}</b>
                    </div>
                </div>
                <div class="p-3 rounded-bottom d-flex flex-column justify-content-end" style="min-height: 20vh;">
                    {{-- <div class="d-flex">
                        @if ($product->stock < 10)
                            <p class="text-danger my-auto">Sisa
                                {{ $product->stock }} Pcs</p>
                        @else
                            <p class="text-danger">&nbsp;</p>
                        @endif
                    </div> --}}
                    <div class="mt-3">
                        <h5 class="mt-3">
                            <b>{{ $product->name }}</b>
                        </h5>
                        @if (!is_null($product->discount_price))
                            @php
                                $percentage_product =
                                    (intval($product->sell_price) - intval($product->discount_price)) /
                                    (intval($product->sell_price) / 100);
                            @endphp
                            <h5 class="text-dark mb-2">
                                Rp.
                                {{ \App\Helpers\NumberFormat::formatCurrency($product->discount_price) }}
                            </h5>
                            <div class="d-flex">
                                <span class="text-muted mr-2 my-auto">
                                    <s>Rp.
                                        {{ \App\Helpers\NumberFormat::formatCurrency($product->sell_price) }}</s>
                                </span>
                                <div class="bg-danger text-white p-1 rounded">
                                    {{ $percentage_product }}%
                                </div>
                            </div>
                        @else
                            <h5 class="text-dark mb-2">
                                Rp.
                                {{ \App\Helpers\NumberFormat::formatCurrency($product->sell_price) }}
                            </h5>
                        @endif
                    </div>
                    <div class="pt-3">
                        <button type="button" onclick="addProduct({{ $product->id }})"
                            class="btn btn-block btn-primary d-flex align-items-center justify-content-center"
                            id="button_product_{{ $product->id }}">
                            <i class="mdi mdi-plus mr-1"></i>Tambah Produk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="row justify-content-center">
    <div class="mt-3">
        {{ $products->links() }}
    </div>
</div>
<script>
    $('.pagination a').on('click', function(event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        let page = $(this).attr('href').split('page=')[1];
        catalogue(page);
    });
</script>
