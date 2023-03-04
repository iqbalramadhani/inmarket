@extends('backend.layouts.app')

@section('content')

<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">#{{ $conversation->title }} (Between @if($conversation->sender != null) {{ $conversation->sender->name }} @endif and @if($conversation->receiver != null) {{ $conversation->receiver->name }} @endif)
            </h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($conversation->messages as $message)
                    <li class="list-group-item px-0">
                        <div class="media mb-2">
                          <img class="avatar avatar-xs mr-3" @if($message->user != null) src="{{ uploaded_asset($message->user->avatar_original) }}" @endif onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                          <div class="media-body">
                            <h6 class="mb-0 fw-600">
                                @if ($message->user != null)
                                    {{ $message->user->name }}
                                @endif
                            </h6>
                            <p class="opacity-50">{{$message->created_at}}</p>
                          </div>
                        </div>
                        <p>
                            {{ $message->message }}
                        </p>
                    </li>
                @endforeach
            </ul>
            @if (Auth::user()->id == $conversation->receiver_id)
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="text-right">
                        <button type="submit" class="btn btn-info">{{translate('Send')}}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@if ($conversation->complain_id != NULL || $conversation->complain_id > 0)
<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">
            Complain Detail
            </h1>
        </div>
        <div class="card-body">
            <span class="text-muted mb-2 d-block">Alasan Komplain</span>
            <p class="d-block">{{$order->complain->reason}}</p>
            <div class="h6 mt-5 d-block text-muted">Status : <span class="fw-700 text-uppercase text-danger">{{$order->complain->status}}</span></div>
            @foreach($order->complain->images()->get() as $image)
                <img class="col-2 p-2" src="{{url('public/'.$image->detail()->file_name)}}" alt="{{$image->detail()->file_original_name}}">
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
