@extends('layouts.app')

@section('content')
<h1 class="px-4 pt-1 pb-3 text-3xl font-bold text-center flex items-center justify-center gap-4">
    <span>My Order History</span>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
    </svg>
</h1>

<div class="flex flex-col w-full max-w-6xl mx-auto gap-6">

    {{-- Orders Section --}}
    @auth
        @if ($orders->isNotEmpty())
            @foreach ($orders as $order)
            <div class="w-full max-w-6xl mb-8 rounded-lg shadow-md border bg-white hover:bg-gray-100 transition duration-200">
                <div class="flex flex-col md:flex-row">

                    {{-- Order Info --}}
                    <div class="md:w-1/5 p-4 border-b md:border-b-0 md:border-r">
                        <p><span class="font-semibold">Order ID:</span> {{ $order->id }}</p>
                        <p><span class="font-semibold">Date:</span> {{ date('jS F Y', strtotime($order->date)) }}</p>
                        <p><span class="font-semibold">Type:</span> <span class="capitalize">{{ $order->type }}</span></p>
                        <p><span class="font-semibold">Total:</span> RM{{ number_format($order->total, 2) }}</p>
                        <!-- Uncomment if deletion is required
                        <button onclick="remove_form_action({{ $order->id }})" class="mt-2 text-red-700 font-semibold border-2 border-red-500 rounded hover:bg-red-500 hover:text-white py-1 px-3">
                            Delete Order
                        </button>
                        -->
                    </div>

                    {{-- Food Items (full row on small screens, full width on large screens) --}}
                    <div class="w-full md:w-4/5 p-4 space-y-4 bg-gray-50">

                        {{-- Special Request / Message --}}
                        @if (!empty($order->message))
                        <div class="mb-4 p-3 border-l-4 border-yellow-500 bg-yellow-50 rounded text-sm text-gray-800">
                            <strong>Customer Note:</strong> {{ $order->message }}
                        </div>
                        @endif

                        @foreach ($order->foods as $food)
<div class="flex items-center bg-white p-3 rounded shadow-sm gap-4">
    {{-- Image --}}
    <img
        src="{{ $food->picture }}"
        alt="{{ $food->name }}"
        class="w-20 h-20 object-cover rounded-md"
    >

    {{-- Info --}}
    <div class="flex flex-col justify-center">
        <h5 class="text-base font-semibold text-gray-900 mb-1">{{ $food->name }}</h5>
        <p class="text-sm text-gray-700 mb-0.5">
            Quantity: <strong>{{ $food->pivot->quantity }}</strong>
        </p>
        <p class="text-sm text-gray-700">
            Price: <strong>RM{{ number_format($food->price * $food->pivot->quantity, 2) }}</strong>
            <span class="text-xs text-gray-500 ml-1">[RM{{ number_format($food->price, 2) }} / unit]</span>
        </p>

        @if (!empty($food->pivot->message))
            <p class="text-sm text-gray-700 italic mt-1">
                Customer Request: "{{ $food->pivot->message }}"
            </p>
        @endif
    </div>
</div>
@endforeach


    </div>

                    {{-- Payment Status --}}




                    {{-- Feedback --}}
                   <div class="md:w-1/4 p-4 flex flex-col items-center justify-center border-t md:border-t-0 md:border-l bg-white">
                        @php $feedback = $order->feedback; @endphp
                        @if ($feedback)
                            <div class="w-full text-center">
                                <h2 class="text-green-700 font-semibold mb-2">Feedback Submitted</h2>
                                <p class="italic text-gray-700 text-sm mb-2 px-2">"{{ $feedback->comment }}"</p>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Thank you for your feedback!</span>
                            </div>
                        @else
                            <div class="w-full">
                                <h2 class="font-semibold mb-2">Leave Feedback</h2>
                                <form method="POST" action="{{ route('orders.feedback', $order->id) }}">
                                    @csrf
                                    <label for="comment-{{ $order->id }}" class="block text-sm font-medium text-gray-700 mb-1">Comment:</label>
                                    <textarea id="comment-{{ $order->id }}" name="comment" rows="2" class="w-full p-2 border rounded mb-2" required></textarea>
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Submit Feedback</button>
                                </form>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        @else
            <p class="text-lg text-center mt-8 text-gray-600">Your order history is empty.</p>
        @endif
    @else
        <p class="text-lg text-center mt-8 text-gray-600">You must be logged in to view your order history.</p>
    @endauth
</div>

{{-- JS for modals (if re-enabled) --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('.openRemoveModal').on('click', function() {
            $('#remove-modal').removeClass('invisible');
        });
        $('.closeRemoveModal').on('click', function() {
            $('#remove-modal').addClass('invisible');
        });
        $('.openPaymentModal').on('click', function() {
            $('#payment-modal').removeClass('invisible');
            setTimeout(() => {
                $('#payment-modal').addClass('invisible');
                $('#payment-success-modal').removeClass('invisible');
                setTimeout(() => $('#payment-success-modal').addClass('invisible'), 3000);
            }, 3000);
        });
    });

    function remove_form_action(order_id) {
        $('#remove_form').attr('action', '/order/' + order_id);
    }
</script>
@endsection
