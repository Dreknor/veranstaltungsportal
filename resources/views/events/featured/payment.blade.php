<x-layouts.app>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Featured Event Zahlung</h1>
        <p class="text-gray-600 mb-6">Bitte schließen Sie die Zahlung ab, um Ihr Event als Featured zu markieren.</p>

        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
            <div><strong>Event:</strong> {{ $featuredEventFee->event->title }}</div>
            <div><strong>Dauer:</strong> {{ ucfirst($featuredEventFee->duration_type) }}</div>
            <div><strong>Start:</strong> {{ $featuredEventFee->featured_start_date->format('d.m.Y') }}</div>
            <div><strong>Betrag:</strong> {{ number_format($featuredEventFee->amount, 2, ',', '.') }} €</div>
        </div>

        <form method="POST" action="{{ route('featured-events.process-payment', $featuredEventFee) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block font-medium mb-1">Zahlungsmethode</label>
                <select name="payment_method" class="w-full border rounded p-2" required>
                    <option value="stripe">Kreditkarte (Stripe)</option>
                    <option value="paypal">PayPal</option>
                    <option value="invoice">Rechnung</option>
                    <option value="bank_transfer">Banküberweisung</option>
                </select>
            </div>
            <div>
                <label class="block font-medium mb-1">Referenz (optional)</label>
                <input type="text" name="payment_reference" class="w-full border rounded p-2" placeholder="z.B. Transaktions-ID">
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Zahlung abschließen</button>
        </form>
    </div>
</x-layouts.app>
