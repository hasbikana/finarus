<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\PendingNotification;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;

class PendingNotificationController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = $request->user()->pendingNotifications()
            ->where('status', 'pending')
            ->latest()
            ->paginate($request->per_page ?? 15);

        return JsonResource::collection($notifications);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:1000',
            'merchant' => 'nullable|string|max:255',
            'notification_date' => 'nullable|date',
            'raw_body' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'source' => 'required|in:push_notif,ocr',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('ocr-receipts', 'public');
        }

        $notification = PendingNotification::create($validated);

        return response()->json([
            'message' => 'Notifikasi berhasil disimpan',
            'notification' => $notification,
        ], 201);
    }

    public function approve(Request $request, PendingNotification $pending_notification): JsonResponse
    {
        $this->authorize('update', $pending_notification);

        if ($pending_notification->status !== 'pending') {
            return response()->json(['message' => 'Notifikasi sudah diproses'], 400);
        }

        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', $request->user()->id)],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', $request->user()->id)],
            'description' => 'nullable|string|max:1000',
        ]);

        $transaction = $this->transactionService->createTransaction([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'account_id' => $validated['account_id'],
            'type' => $pending_notification->type,
            'amount' => $pending_notification->amount,
            'description' => $validated['description'] ?? $pending_notification->merchant ?? $pending_notification->description,
            'transaction_date' => $pending_notification->notification_date ?? now()->toDateString(),
            'is_pending' => false,
            'pending_source' => $pending_notification->source,
        ]);

        $pending_notification->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat dari notifikasi',
            'transaction' => $transaction,
        ]);
    }

    public function reject(Request $request, PendingNotification $pending_notification): JsonResponse
    {
        $this->authorize('delete', $pending_notification);

        if ($pending_notification->status !== 'pending') {
            return response()->json(['message' => 'Notifikasi sudah diproses'], 400);
        }

        $pending_notification->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Notifikasi ditolak',
        ]);
    }

    public function count(Request $request): JsonResponse
    {
        $count = $request->user()->pendingNotifications()
            ->where('status', 'pending')
            ->count();

        return response()->json(['pending_count' => $count]);
    }
}
