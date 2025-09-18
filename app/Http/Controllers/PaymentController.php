<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRefundRequest;
use App\Repositories\PaymentRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class PaymentController extends BaseController
{
    /**
     * Show payment index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('payment.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadPaymentList(Request $request)
    {
        try {
            $results = PaymentRepository::loadPaymentList($request);
            $view = View::make('payment._list', ['data' => $results])->render();
            $pagination = getPaginationLink($results);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $results, 'html' => $view, 'pagination' => $pagination],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Refund Payment
     *
     * @return \Illuminate\Http\Response
     */
    public function refundPayment(PaymentRefundRequest $request)
    {
        try {
            $result = PaymentRepository::refundPayment($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Refund successfully initiated.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Payment Invoice Detail
     *
     * @return \Illuminate\Http\Response
     */
    public function invoiceDetail(Request $request)
    {
        try {
            $result = PaymentRepository::invoiceDetail($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Invoice detail.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Show the billing detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        try {
            $data = PaymentRepository::detail($request);
            if (! empty($data)) {
                return view('payment.view', compact('data'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    public function paymentFailedNotificationCron(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return PaymentRepository::paymentFailedNotificationCron($request);
        }, 'Notification cron run successfully.');
    }

    public function notifyToUser(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return PaymentRepository::notifyToUser($request);
        }, 'Notification sent to user.');
    }

    public function sendPaymentFailedNotification(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return PaymentRepository::sendPaymentFailedNotification($request);
        }, 'Notification cron run successfully.');
    }
    
}
