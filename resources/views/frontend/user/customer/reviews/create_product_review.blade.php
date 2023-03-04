@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Review Product</h5>
        </div>

        <form action="{{route('post-review.product.store', ['slug' => $product->slug, 'id' => encrypt($product->id), 'order_id' => $order_id])}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row align-items-center" style="margin: 10px 0 10px   0;">
            <div class="col-2">
                <img
                    class="img-fit lazyload mx-auto h-140px h-md-210px"
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                    style="border: 1px solid #e2e5ec; opacity: 1; padding: 5px 5px 5px 5px; margin: 10px 10px 10px 10px;"
                >
            </div>
            <div class="col-10 text-left">
                <div id="is_reviewed" class="col-12 border rounded mt-3}}">
                    <div class="d-flex p-2 d-block-flex bd-highlight align-items-center">
                        <div class="flex-row flex-fill">
                            <h6>{{ $product->getTranslation('name') }}</h6>
                            <strong class="">Berikan ulasan untuk produk ini</strong> 

                            <div class="form-group mt-2">
                                <div class="rating ">
                                    <span class="rating__result"></span>
                                    <i class="rating__star far fa-star"></i>
                                    <i class="rating__star far fa-star"></i>
                                    <i class="rating__star far fa-star"></i>
                                    <i class="rating__star far fa-star"></i>
                                    <i class="rating__star far fa-star"></i>
                                </div>
                            </div>

                            <div class="form-group mt-2">
                                <textarea class="form-control" id="exampleFormControlTextarea1" name="comment" rows="3"></textarea>
                            </div>
                            <input type="hidden" name="rating">

                            <!-- <span class="d-block text-muted mt-1">{{translate('Order akan selesai secara otomatis pada ')}} {{'timer'}}</span> -->

                            <button type="submit" class="btn btn-primary btn-xs flex-right mb-2">Kirim</button>
                        </div>
                    </div>      
                </div>
            </div>
        </div>

            <!-- <button type="submit" class="btn btn-primary">Kirim</button> -->
        </form>

    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">

    <style>

        .rating {
            position: relative;
            width: 180px;
            background: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: .3em;
            padding: 5px;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 0 2px #b3acac;
        }


        .rating__star {
            font-size: 1.3em;
            cursor: pointer;
            color: #dabd18b2;
            transition: filter linear .3s;
        }

        .rating__star:hover {
            filter: drop-shadow(1px 1px 4px gold);
        }

    </style>
    @endsection

@section('script')
    <script>
        const ratingStars = [...document.getElementsByClassName("rating__star")];
        const ratingResult = document.querySelector(".rating__result");

        printRatingResult(ratingResult);

        function executeRating(stars, result) {
            const starClassActive = "rating__star fas fa-star";
            const starClassUnactive = "rating__star far fa-star";
            const starsLength = stars.length;
            let i;
            stars.map((star) => {
                star.onclick = () => {
                    i = stars.indexOf(star);

                    if (star.className.indexOf(starClassUnactive) !== -1) {
                        printRatingResult(result, i + 1);
                        for (i; i >= 0; --i) stars[i].className = starClassActive;
                    } else {
                        printRatingResult(result, i);
                        for (i; i < starsLength; ++i) stars[i].className = starClassUnactive;
                    }
                };
            });
        }

        function printRatingResult(result, num = 0) {
            result.textContent = `${num}/5`;
            $('input[name="rating"]').val(num)
        }

        executeRating(ratingStars, ratingResult);
    </script>
@endsection
