<?php

namespace App\Services\Shop\Payments;

use App\Models\Shop\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * FedaPay (Mobile Money et cartes). Le statut d'un paiement est toujours relu auprès de l'API :
 * ni le retour du navigateur ni le contenu d'une notification ne suffisent à marquer une commande payée.
 */
class FedaPayGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'fedapay';
    }

    public function checkout(Payment $payment): string
    {
        $order = $payment->order;

        $created = $this->client()->post('/v1/transactions', [
            'description' => 'Commande '.$order->number,
            'amount' => $payment->amount,
            'currency' => ['iso' => config('shop.currency')],
            'callback_url' => route('shop.payment.return', $payment),
            'customer' => [
                'firstname' => $order->first_name,
                'lastname' => $order->last_name,
                'email' => $order->email,
            ],
        ])->throw()->json();

        $transaction = $this->transaction($created);
        $id = $transaction['id'] ?? null;

        if (! $id) {
            throw new RuntimeException('Réponse FedaPay inattendue à la création de la transaction.');
        }

        $token = $this->client()->post('/v1/transactions/'.$id.'/token')->throw()->json();
        $url = $token['url'] ?? Arr::get($token, 'token.url');

        if (! $url) {
            throw new RuntimeException('FedaPay n’a pas renvoyé d’adresse de paiement.');
        }

        $payment->update(['reference' => (string) $id, 'checkout_url' => $url, 'payload' => ['transaction' => Arr::only($transaction, ['id', 'status', 'amount'])]]);

        return $url;
    }

    public function status(Payment $payment): string
    {
        if (! $payment->reference) {
            return 'pending';
        }

        $transaction = $this->transaction($this->client()->get('/v1/transactions/'.$payment->reference)->throw()->json());

        if ((int) ($transaction['amount'] ?? 0) !== $payment->amount) {
            return 'pending';
        }

        return match ($transaction['status'] ?? null) {
            'approved', 'transferred' => 'approved',
            'declined' => 'declined',
            'canceled', 'cancelled', 'expired' => 'canceled',
            default => 'pending',
        };
    }

    public function referenceFromWebhook(Request $request): ?string
    {
        $secret = (string) config('services.fedapay.webhook_secret');
        $header = (string) $request->header('X-FEDAPAY-SIGNATURE');
        $payload = $request->getContent();

        if ($secret === '' || $header === '') {
            return null;
        }

        $parts = [];
        foreach (explode(',', $header) as $piece) {
            [$key, $value] = array_pad(explode('=', trim($piece), 2), 2, null);
            $parts[$key][] = $value;
        }

        $timestamp = $parts['t'][0] ?? null;

        if (! $timestamp || abs(time() - (int) $timestamp) > 600) {
            return null;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
        $valid = collect($parts['s'] ?? $parts['v1'] ?? [])->contains(fn ($signature) => is_string($signature) && hash_equals($expected, $signature));

        if (! $valid) {
            return null;
        }

        $data = json_decode($payload, true) ?: [];
        $id = Arr::get($data, 'entity.id') ?? Arr::get($data, 'data.id') ?? Arr::get($data, 'object.id');

        return $id ? (string) $id : null;
    }

    private function client(): PendingRequest
    {
        $secret = config('services.fedapay.secret_key');

        if (! $secret) {
            throw new RuntimeException('Clé FedaPay absente : renseignez FEDAPAY_SECRET_KEY.');
        }

        $base = config('services.fedapay.environment') === 'live' ? 'https://api.fedapay.com' : 'https://sandbox-api.fedapay.com';

        return Http::baseUrl($base)->withToken($secret)->acceptJson()->asJson()->timeout(20);
    }

    private function transaction(?array $response): array
    {
        $response ??= [];

        return $response['v1/transaction'] ?? $response['transaction'] ?? $response;
    }
}
