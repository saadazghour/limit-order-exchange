<?php

namespace App\Services;

use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchingService
{
    const COMMISSION_RATE = 0.015; // 1.5%

    /**
     * Match a given order with existing counter-orders.
     *
     * @param Order $order The order to be matched.
     * @return void
     */
    public function match(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $query = Order::where('symbol', $order->symbol)
                ->where('status', Order::STATUS_OPEN)
                ->where('user_id', '!=', $order->user_id)
                ->orderBy('created_at', 'asc');

            if ($order->side === 'buy') {
                $query->where('side', 'sell')->where('price', '<=', $order->price);
            } else { // sell order
                $query->where('side', 'buy')->where('price', '>=', $order->price);
            }

            $match = $query->lockForUpdate()->first();

            if ($match) {
                // Determine buyer and seller
                $buyerOrder = $order->side === 'buy' ? $order : $match;
                $sellerOrder = $order->side === 'sell' ? $order : $match;

                $buyer = User::find($buyerOrder->user_id);
                $seller = User::find($sellerOrder->user_id);

                // Trade details
                $tradePrice = $match->price; // The price is determined by the existing order on the book
                $tradeAmount = min($buyerOrder->amount, $sellerOrder->amount); // Full match only
                $tradeValue = $tradeAmount * $tradePrice;
                $commission = $tradeValue * self::COMMISSION_RATE;

                // --- Execute the trade ---

                // 1. Handle Seller: give asset, receive USD (less commission)
                $sellerAsset = Asset::where('user_id', $seller->id)->where('symbol', $sellerOrder->symbol)->lockForUpdate()->first();
                $sellerAsset->locked_amount -= $tradeAmount;
                $seller->balance += ($tradeValue - $commission);
                $seller->save();
                $sellerAsset->save();

                // 2. Handle Buyer: receive asset, give USD
                $buyerAsset = Asset::where('user_id', $buyer->id)->where('symbol', $buyerOrder->symbol)->lockForUpdate()->firstOrCreate(['user_id' => $buyer->id, 'symbol' => $buyerOrder->symbol]);
                $buyerAsset->amount += $tradeAmount;
                $buyer->balance += ($buyerOrder->price * $tradeAmount) - $tradeValue;
                $buyer->save();
                $buyerAsset->save();

                // 3. Update orders status
                $buyerOrder->status = Order::STATUS_FILLED;
                $sellerOrder->status = Order::STATUS_FILLED;
                $buyerOrder->save();
                $sellerOrder->save();

                // 4. Create Trade record
                $trade = Trade::create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'buy_order_id' => $buyerOrder->id,
                    'sell_order_id' => $sellerOrder->id,
                    'symbol' => $order->symbol,
                    'price' => $tradePrice,
                    'amount' => $tradeAmount,
                    'commission_amount' => $commission,
                ]);

                // 5. Broadcast event
                OrderMatched::dispatch($trade);

                Log::info("Trade matched and executed for order ID: {$order->id}");
            }
        });
    }
}
