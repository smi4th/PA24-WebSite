@push('reviews_prestations')
    <link rel="stylesheet" href="{{ asset('css/profile/reviews_prestations.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
@endpush

@section('content')
    <div class="container_layout">
        <div class="title_reviews">
            <h1>Avis</h1>
        </div>
        <div class="content_reviews">
            @if(count($reviews) == 0)
                <h3>Aucun avis sur vos prestations pour le moment</h3>
            @endif
            @foreach($reviews as $review)
                <div class="review">
                    <div class="review_header">
                        <h3>{{$review->title}}</h3>
                        <p>{{$review->created_at}}</p>
                    </div>
                    <div class="review_content">
                        <p>{{$review->content}}</p>
                    </div>
                    <div class="review_footer">
                        <p>{{$review->note}}/5</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
