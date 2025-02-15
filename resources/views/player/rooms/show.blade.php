@extends('layouts.player')

@section('title', 'Sala de Bingo')

@section('content')
    <h2 class="text-2xl font-bold text-green-500 text-center">🎲 {{ $room->name }} 🎲</h2>
    <p class="text-center text-gray-600">{{ $room->description }}</p>

    <div class="text-center mt-4">
        <p><strong>💰 Premios Totales:</strong> Bs {{ $room->total_prizes }}</p>
        <p><strong>🎟️ Costo por Cartón:</strong> Bs {{ $room->card_price }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500 text-white text-center p-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500 text-white text-center p-2 rounded-md mb-4">
            {{ session('error') }}
        </div>
    @endif

    <h3 class="text-lg font-bold mt-6 text-center">📜 Cartones Disponibles</h3>

    <div class="grid grid-cols-8 gap-2 mt-4 text-sm">
        @foreach($availableCards as $card)
            <button id="card-{{ $card->id }}"
                class="card-btn bg-black text-white p-2 rounded-md border border-green-500 hover:bg-green-500 hover:text-white transition text-xs"
                onclick="showCardDetails({{ $card->id }}, '{{ $card->card_data }}')">
                {{ $card->card_number }}
            </button>
        @endforeach
    </div>

    <!-- Modal de Cartón -->
    <div id="cardModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-md shadow-md w-96">
            <h3 class="text-lg font-bold mb-2">Detalles del Cartón #<span id="cardNumber"></span></h3>
            <table class="w-full border">
                <thead>
                    <tr>
                        <th class="p-2 border bg-orange-500 text-white">B</th>
                        <th class="p-2 border bg-orange-500 text-white">I</th>
                        <th class="p-2 border bg-orange-500 text-white">N</th>
                        <th class="p-2 border bg-orange-500 text-white">G</th>
                        <th class="p-2 border bg-orange-500 text-white">O</th>
                    </tr>
                </thead>
                <tbody id="cardTableBody"></tbody>
            </table>
            <div class="flex justify-between mt-4">
                <button onclick="closeCardModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cerrar</button>
                <button onclick="selectCard()" id="selectCardButton" class="bg-green-500 text-white px-4 py-2 rounded-md">
                    Seleccionar Cartón
                </button>
            </div>
        </div>
    </div>

    <!-- Botón flotante para confirmar compra -->
    <div id="confirmPurchaseButton" class="hidden fixed bottom-5 left-1/2 transform -translate-x-1/2">
        <button onclick="confirmPurchase()" class="px-5 py-2 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600">
            Ir a Pagar <span id="cart-count"></span> Cartones (<span id="total-price"></span> Bs)
        </button>
    </div>

    <form id="buyCardForm" action="{{ route('player.buy.card', ['roomId' => $room->id]) }}" method="POST">
    @csrf
    <input type="hidden" name="selected_cards" id="selectedCards">
</form>


    <script>
        let selectedCards = [];
        let cardPrice = {{ $room->card_price }};
        let currentCardId = null;

        function showCardDetails(cardId, cardData) {
            currentCardId = cardId;
            document.getElementById('cardNumber').innerText = cardId;

            let card = JSON.parse(cardData);
            let tableBody = document.getElementById('cardTableBody');
            tableBody.innerHTML = '';

            for (let i = 0; i < 5; i++) {
                let row = document.createElement('tr');
                ['B', 'I', 'N', 'G', 'O'].forEach((letter, index) => {
                    let cell = document.createElement('td');
                    cell.className = 'p-2 border text-center text-sm';

                    if (letter === 'N' && i === 2) {
                        cell.innerHTML = '<span class="text-red-500 font-bold">X</span>';
                    } else {
                        cell.innerText = card[letter][i];
                    }

                    row.appendChild(cell);
                });
                tableBody.appendChild(row);
            }

            document.getElementById('cardModal').classList.remove('hidden');

            let selectButton = document.getElementById('selectCardButton');
            if (selectedCards.includes(currentCardId)) {
                selectButton.innerText = "Deseleccionar";
                selectButton.classList.replace('bg-green-500', 'bg-red-500');
            } else {
                selectButton.innerText = "Seleccionar Cartón";
                selectButton.classList.replace('bg-red-500', 'bg-green-500');
            }
        }

        function closeCardModal() {
            document.getElementById('cardModal').classList.add('hidden');
        }

        function selectCard() {
            let cardButton = document.getElementById(`card-${currentCardId}`);
            let index = selectedCards.indexOf(currentCardId);
            let selectButton = document.getElementById('selectCardButton');

            if (index === -1) {
                selectedCards.push(currentCardId);
                cardButton.classList.add('bg-green-500', 'text-white');
                selectButton.innerText = "Deseleccionar";
                selectButton.classList.replace('bg-green-500', 'bg-red-500');
            } else {
                selectedCards.splice(index, 1);
                cardButton.classList.remove('bg-green-500', 'text-white');
                cardButton.classList.add('bg-black', 'text-white');
                selectButton.innerText = "Seleccionar Cartón";
                selectButton.classList.replace('bg-red-500', 'bg-green-500');
            }

            updateConfirmButton();
            closeCardModal();
        }

        function updateConfirmButton() {
            let button = document.getElementById('confirmPurchaseButton');
            let countText = document.getElementById('cart-count');
            let totalText = document.getElementById('total-price');

            if (selectedCards.length > 0) {
                button.classList.remove('hidden');
                countText.innerText = selectedCards.length;
                totalText.innerText = selectedCards.length * cardPrice;
            } else {
                button.classList.add('hidden');
            }
        }

        function confirmPurchase() {
            if (selectedCards.length === 0) {
                alert('No has seleccionado ningún cartón.');
                return;
            }

            document.getElementById('selectedCards').value = JSON.stringify(selectedCards);
            document.getElementById('buyCardForm').submit();
        }
    </script>
@endsection
