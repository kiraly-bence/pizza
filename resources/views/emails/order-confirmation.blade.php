<x-mail::message>
# Köszönjük a rendelést, {{ $user->name }}!

Megrendelésedet sikeresen fogadtuk. Az alábbiakban találod a részleteket.

---

## Rendelt tételek

<x-mail::table>
| Termék | Db | Egységár | Összesen |
|:-------|---:|--------:|--------:|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ number_format($item->price, 0, ',', ' ') }} Ft | {{ number_format($item->price * $item->quantity, 0, ',', ' ') }} Ft |
@endforeach
</x-mail::table>

---

## Összesítő

| | |
|:---|---:|
| Termékek összesen | {{ number_format($order->subtotal, 0, ',', ' ') }} Ft |
| Kiszállítási díj | {{ number_format($order->delivery_fee, 0, ',', ' ') }} Ft |
| Szolgáltatási díj | {{ number_format($order->service_fee, 0, ',', ' ') }} Ft |
@if ($order->discount_amount > 0)
| Kedvezmény (kupon) | −{{ number_format($order->discount_amount, 0, ',', ' ') }} Ft |
@endif
| **Fizetendő összeg** | **{{ number_format($order->total, 0, ',', ' ') }} Ft** |

---

## Szállítási cím

{{ $order->zip }} {{ $order->city }}, {{ $order->street }}
@if ($order->note)
{{ $order->note }}
@endif

**Fizetési mód:** {{ $order->payment_method === 'card' ? 'Bankkártyás fizetés' : 'Készpénz' }}

---

Hamarosan felvesszük veled a kapcsolatot a rendelés állapotáról.

{{ config('app.name') }}
</x-mail::message>
