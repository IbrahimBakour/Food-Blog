@extends('layouts.app')

@section('content')
<div class="px-3 py-2 flex flex-col justify-center items-center h-[70vh]">
    <div class="flex flex-row leading-normal border shadow-md rounded-lg max-w-3xl min-w-[50%] bg-white max-h-[96%] min-h-[50%]">
        <!-- Food Image Section -->
        <div class="flex flex-col justify-center h-full max-w-[60%] min-w-[50%]">
            <img src="{{ $food['picture'] }}" class='object-cover xl:rounded-l-lg h-full'>
        </div>

        <!-- Food Detail and Actions -->
        <div class="flex flex-col p-4 h-full flex-grow">
            <div class="flex-grow flex flex-col justify-around">
                <h1 class="font-bold font-sans text-2xl leading-8">{{ $food['name'] }}</h1>
                <h1 class="font-semibold font-sans text-lg text-red-500 leading-8">RM {{ $food['price'] }}</h1>
                <h1 class="font-serif text-sm text-gray-600 leading-7">{{ $food['description'] }}</h1>
            </div>

            <div class="pt-3 flex flex-col flex-grow justify-around">
                <!-- Quantity Control -->
                <div>
                    <h1 class="font-sans text-sm text-gray-900 leading-8">Quantity</h1>
                    <div class="inline-flex rounded-md border-2">
                        <button class="py-2 px-3 font-bold border-r-2" id="minusBtn">-</button>
                        <p class="py-2 px-3 text-sm m-0" id="qty" data-object="{{ json_encode($food) }}">1</p>
                        <button class="py-2 px-3 font-bold border-l-2" id="plusBtn">+</button>
                    </div>
                </div>

                <!-- Special Request / Message Input -->
                <div class="mt-4">
                    <label for="menuMessage" class="block text-sm font-medium text-gray-700">Special Request / Message</label>
                    <textarea id="menuMessage" rows="3" class="w-full mt-1 border border-gray-300 rounded-md p-2" placeholder="E.g. Less spicy, no onions, etc."></textarea>
                </div>

                <!-- Add to Cart Button -->
                <button id='addCartBtn' class="p-2 mt-4 bg-blue-600 hover:text-blue-600 text-neutral-50 rounded-md hover:bg-white border-2 border-blue-600 disabled:text-slate-500 disabled:bg-slate-200">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script type="text/javascript">
    function docReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            fn();
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    docReady(() => {
        const plusBtn = document.getElementById('plusBtn');
        const minusBtn = document.getElementById('minusBtn');
        const qty = document.getElementById('qty');
        const addCartBtn = document.getElementById('addCartBtn');
        const messageInput = document.getElementById('menuMessage');
        const csrf = document.querySelector("meta[name='csrf-token']");

        plusBtn.addEventListener('click', () => {
            qty.innerHTML = Number(qty.innerHTML) + 1;
        });

        minusBtn.addEventListener('click', () => {
            if (Number(qty.innerHTML) > 1) {
                qty.innerHTML = Number(qty.innerHTML) - 1;
            }
        });

        addCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addCartBtn.disabled = true;
            addToCart();
        });

        async function addToCart() {
    data = {
        ...JSON.parse(qty.dataset.object),
        quantity: qty.innerHTML,
        message: document.getElementById('menuMessage').value,
        _token: csrf.getAttribute('content'),
    }

    await $.ajax({
        url: '../addToCart',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: (route) => {
            window.location.href = '..' + route;
        }
    });
}

    });
</script>
@endsection
