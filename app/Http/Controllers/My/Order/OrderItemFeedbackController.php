<?php

namespace App\Http\Controllers\My\Order;

use App\Exceptions\Feedback\FeedbackAlreadyExistsException;
use App\Exceptions\Order\OrderAccessDeniedException;
use App\Exceptions\Order\OrderIsNotCompletedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyOrder\OrderItemFeedbackEditRequest;
use App\Models\Feedback;
use App\Models\OrderItem;
use App\Services\FeedbackService;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderItemFeedbackController extends Controller
{
    public function __construct(
        private readonly Toaster $toaster,
    ) {
    }

    public function create(OrderItem $orderItem): Response
    {
        return Inertia::render('my/orders/feedback/OrderItemFeedbackCreateModal', [
            'orderItem' => $orderItem,
            'product' => $orderItem->product,
        ]);
    }

    public function store(
        OrderItem $orderItem,
        OrderItemFeedbackEditRequest $request,
        FeedbackService $feedbackService,
    ): RedirectResponse {
        try {
            $feedback = $feedbackService->createFeedback(
                auth()->user(),
                $orderItem,
                $request->input('is_positive'),
                $request->input('comment')
            );

            if ($feedback->isPositive() && $feedback->hasComment()) {
                $this->toaster->success('Добавлен позитивный отзыв');
            } elseif ($feedback->isPositive() && !$feedback->hasComment()) {
                $this->toaster->success('Добавлена позитивная оценка');
            } elseif ($feedback->isNegative() && $feedback->hasComment()) {
                $this->toaster->error('Добавлен негативный отзыв');
            } elseif ($feedback->isNegative() && !$feedback->hasComment()) {
                $this->toaster->error('Добавлена негативная оценка');
            }

            return redirect()->back();
        } catch (FeedbackAlreadyExistsException|OrderAccessDeniedException|OrderIsNotCompletedException $exception) {
            $this->toaster->error($exception->getMessage());

            return redirect()->back();
        }
    }

    public function edit(OrderItem $orderItem, Feedback $feedback): Response
    {
        return Inertia::render('my/orders/feedback/OrderItemFeedbackUpdateModal', [
            'orderItem' => $orderItem,
            'product' => $orderItem->product,
            'feedback' => $feedback,
        ]);
    }

    public function update(
        OrderItem $orderItem,
        Feedback $feedback,
        OrderItemFeedbackEditRequest $request,
        FeedbackService $feedbackService,
    ): RedirectResponse {
        $feedbackService->updateFeedback($feedback, $request->input('is_positive'), $request->input('comment'));

        if ($feedback->hasComment()) {
            $this->toaster->success('Отзыв изменен');
        } else {
            $this->toaster->success('Оценка изменена');
        }

        return redirect()->back();
    }

    public function destroy(
        OrderItem $orderItem,
        Feedback $feedback,
        FeedbackService $feedbackService,
    ): RedirectResponse {
        $feedbackService->removeFeedback($feedback);
        $this->toaster->error('Оценка удалена');

        return redirect()->back();
    }
}
