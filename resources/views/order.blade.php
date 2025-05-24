@extends('layouts.app')

@section('content')
<h1 class="px-4 pt-1 pb-3 text-3xl font-bold text-center">
    <span class="mr-5 self-center"> My Order History </span>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 inline self-center" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
    </svg>
</h1>

<div class="flex flex-col items-center justify-center min-h-[70vh]">
    {{-- Orders --}}
    @if (auth()->check())
        @if (count($orders) != 0)
            @foreach ($orders as $order)

                    <div class="flex flex-row justify-between w-full max-w-5xl mx-auto mb-8 bg-white rounded-lg shadow-md border hover:bg-gray-100">
                    <!-- Order Info -->
                    <div class="p-4 w-1/5 flex flex-col justify-between border-r">
                        <div>
                            <p class="mb-1"> <span class="font-semibold">Order ID:</span> {{$order->id}} </p>
                            <p class="mb-1"> <span class="font-semibold">Date:</span> {{date_format(date_create($order->date), 'jS F Y')}} </p>
                            <p class="mb-1"> <span class="font-semibold">Type:</span> <span class="capitalize">{{$order->type}}</span> </p>
                            <p class="mb-1"> <span class="font-semibold">Total:</span> RM{{number_format((float)$order->total, 2, '.', '')}} </p>
                        </div>
                        <div class="mt-2">
                            <button onclick="remove_form_action({{$order->id}})" type="button" class="openRemoveModal text-red-700 font-semibold bg-inherit border-red-500 rounded hover:text-white hover:bg-red-500 hover:border-transparent py-1 px-3 border-2">
                                <span> Delete Order </span>
                            </button>
                        </div>
                    </div>
                    <!-- Food Items -->
                    <div class="w-3/5 flex flex-col justify-center px-4 py-2">
                        @foreach ($order->food as $food)
                        <div class="flex flex-row items-center mb-3 bg-gray-50 rounded shadow-sm p-2">
                            <img class="h-40 w-40 object-cover rounded-lg mr-4" src="{{$food->picture}}" alt="">
                            <div>
                                <h5 class="mb-1 text-lg font-bold text-gray-900"> {{$food->name}} </h5>
                                <p class="text-gray-700 text-sm">Quantity: <b>{{$food->pivot->quantity}}</b></p>
                                <p class="text-gray-700 text-sm">Price: <b>RM{{number_format((float)($food->price*$food->pivot->quantity), 2, '.', '')}}</b>
                                    <span class="opacity-60 text-xs">[RM{{number_format((float)($food->price), 2, '.', '')}} per unit]</span>
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Feedback Section -->
                    <div class="w-1/4 flex flex-col items-center justify-center bg-gray-50 border-l px-4 py-6">
                        @php
                            $feedback = $order->feedback ?? null; // If you eager load feedback relation
                        @endphp
                        @if($feedback)
                            <div class="w-full text-center">
                                <h2 class="font-semibold mb-2 text-green-700">Feedback Submitted</h2>
                                <div class="text-gray-700 text-sm italic mb-2 px-2">"{{ $feedback->comment }}"</div>
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Thank you for your feedback!</span>
                            </div>
                        @else
                            <div class="w-full">
                                <h2 class="font-semibold mb-2">Leave Feedback</h2>
                                <form method="POST" action="{{ route('orders.feedback', $order->id) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label for="comment-{{$order->id}}" class="block text-sm font-medium text-gray-700">Comment:</label>
                                        <textarea id="comment-{{$order->id}}" name="comment" rows="2" class="mt-1 block w-full border rounded p-2" required></textarea>
                                    </div>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Submit Feedback</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p class="px-4 pt-4 text-lg text-center">
                Your order history is empty.
            </p>
        @endif
    @else
        <p class="px-4 pt-4 text-lg text-center">
            You must be logged in to view your order history.
        </p>
    @endif
</div>

<div class="py-10"></div>

<script type="text/javascript">


    // Remove modal logic
    $(document).ready(function() {
        $('.openRemoveModal').on('click', function(e) {
            $('#remove-modal').removeClass('invisible');
        });
        $('.closeRemoveModal').on('click', function(e) {
            $('#remove-modal').addClass('invisible');
        });
        $('.openPaymentModal').on('click', function(e) {
            $('#payment-modal').removeClass('invisible');
            setTimeout(function() {
                $('#payment-modal').addClass('invisible');
                showPaymentSuccess();
            }, 3000);
        });

        function showPaymentSuccess() {
            $('#payment-success-modal').removeClass('invisible');
            setTimeout(function() {
                $('#payment-success-modal').addClass('invisible');
            }, 3000);
        }
    });

    function remove_form_action(order_id) {
        $('#remove_form').attr('action', '/order/' + order_id);
    }
</script>
@endsection

