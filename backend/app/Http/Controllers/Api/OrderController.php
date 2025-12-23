<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of open orders for a given symbol.
     */
    public function index(Request $request)
    {
        $request->validate([
            'symbol' => ['required', 'string', 'max:10'],
        ]);

        $orders = Order::where('symbol', $request->symbol)
            ->where('status', Order::STATUS_OPEN)
            ->oldest() // Match with oldest first
            ->get();

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        return DB::transaction(function () use ($user, $validated) {
            $order = new Order($validated);
            $order->user_id = $user->id;
            $order->status = Order::STATUS_OPEN;

            // Handle Buy Order: Deduct USD from balance
            if ($order->side === 'buy') {
                $totalUsd = $order->amount * $order->price;
                if ($user->balance < $totalUsd) {
                    throw new \Exception('Insufficient USD balance to place buy order.');
                }
                // Decrement balance and save
                $user->balance -= $totalUsd;
                $user->save();
            }
            // Handle Sell Order: Lock assets
            else { // side === 'sell'
                $asset = $user->assets()->firstOrCreate(
                    ['symbol' => $order->symbol],
                    ['amount' => 0, 'locked_amount' => 0]
                );

                if ($asset->amount < $order->amount) {
                    throw new \Exception('Insufficient asset amount to place sell order.');
                }
                // Lock asset amount and save
                $asset->amount -= $order->amount;
                $asset->locked_amount += $order->amount;
                $asset->save();
            }

            $order->save();

            // TODO: Dispatch MatchOrderJob after saving the order
            // MatchOrderJob::dispatch($order);

            return OrderResource::make($order);
        });
    /**
     * Cancel an open order.
     */
    public function cancel(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to cancel this order.'], 403);
        }

        if ($order->status !== Order::STATUS_OPEN) {
            return response()->json(['message' => 'Only open orders can be cancelled.'], 400);
        }

        return DB::transaction(function () use ($user, $order) {
            $order->status = Order::STATUS_CANCELLED;
            $order->save();

            // Release locked funds/assets
            if ($order->side === 'buy') {
                $user->balance += ($order->amount * $order->price);
                $user->save();
            } else { // side === 'sell'
                $asset = $user->assets()->where('symbol', $order->symbol)->first();
                if ($asset) {
                    $asset->amount += $order->amount;
                    $asset->locked_amount -= $order->amount;
                    $asset->save();
                }
            }

            return response()->json(['message' => 'Order cancelled successfully.'], 200);
        });
    }
}
