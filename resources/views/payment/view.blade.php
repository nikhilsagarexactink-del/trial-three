@extends('layouts.app')
<title>Billing Plan</title>
@section('content')
@include('layouts.sidebar')
@php 
$userType = userType();
$subscriptions = $data['detail'];
$paymentHistory = $data['paymentHistory'];
$userData = getUser();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{route('user.managePayments',['user_type'=>$userType])}}">Payment</a></li>
                    <li class="breadcrumb-item view" aria-current="page">Payment History</li>
                </ol>
            </nav>
            <h2 class="page-title text-capitalize mb-0">Payment History </h2>
        </div>
        @if($userData->user_type == 'admin' && !empty($subscriptions) && $subscriptions->stripe_status == 'pending')
            <div class="right-side mt-2 mt-md-0">
                <a href="javascript:void(0);" onclick="sendNotification({{$subscriptions->user_id}})" class="btn btn-secondary ripple-effect-dark text-white" title="Send Notification">
                    Notify to User
                </a>
            </div>
        @endif
    </div>
    <section class="content white-bg ">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-4 col-md-5 xs-margin-30px-bottom">
                            <div class="team-single-img">
                               <img src="{{getUserImage($subscriptions->profile_image, 'profile-pictures')}}" alt="">
                            </div>
                            <div class="bg-light-gray padding-30px-all md-padding-25px-all sm-padding-20px-all">
                                <h4 class="margin-10px-bottom font-size24 md-font-size22 sm-font-size20 font-weight-600">{{ucfirst($subscriptions->first_name.' '.$subscriptions->last_name)}}</h4>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-7">
                            <div class="team-single-text padding-50px-left sm-no-padding-left">
                                <div class="contact-info-section margin-40px-tb">
                                    <ul class="list-style9 no-margin">
                                        <li>
                                            <div class="row">
                                                <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Email:</strong></div>
                                                <div class="col-md-7 col-7">
                                                    <p>{{$subscriptions->email}}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="row">
                                                <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Plan Name:</strong></div>
                                                <div class="col-md-7 col-7">
                                                    <p>{{!empty($subscriptions->plan_name) ? ucfirst($subscriptions->plan_name) : '-'}}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="row">
                                                <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Payment Date:</strong></div>
                                                <div class="col-md-7 col-7">
                                                    <p>{{!empty($subscriptions->created_at) ? getLocalDateTime($subscriptions->created_at, 'm-d-Y g:i A') : '-'}}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="row">
                                                <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Plan Type:</strong></div>
                                                <div class="col-md-7 col-7">
                                                    <p>{{!empty($subscriptions->subscription_type) ? ucfirst($subscriptions->subscription_type) : '-'}}</p>
                                                </div>
                                            </div>
                                        </li>
                                        <!-- <li>
                                            <div class="row">
                                                <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Amount:</strong></div>
                                                <div class="col-md-7 col-7">
                                                    <p>${{!empty($subscriptions->grade) ? ($subscriptions->subscription_type=='free' ? '0' : ($subscriptions->subscription_type=='monthly' ? $subscriptions->cost_per_month : $subscriptions->cost_per_year)) : '0'}}</p>
                                                </div>
                                            </div>
                                        </li> -->
                                        <li>
                                            <div class="row">
                                                <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Subscription Status:</strong></div>
                                                <div class="col-md-7 col-7">
                                                    <p>{{!empty($subscriptions->stripe_status) ? ucfirst($subscriptions->stripe_status) : '-'}}</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(count($paymentHistory) > 0)
            <div class="mb-3  text-end">
                <a class="btn btn-primary" onClick="getInvoiceDetail({{$subscriptions}}, 'invoice')" href="javascript:void(0);">Invoice</a>
            </div>
        @endif
        <div class="mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Plan</th>
                        <th>Plan Type</th>
                        <th>Date</th>
                        <th>Subscription Status</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($paymentHistory) > 0)
                        @foreach($paymentHistory as $payment)
                            <tr>
                                <td>{{ number_format($payment['amount'], 2) }}</td>
                                <td>{{ strtoupper($payment['currency']) }}</td>
                                <td>{{ ucfirst($payment['plan_name']) }}</td>
                                <td>{{ ucfirst($payment['interval']) }}</td>
                                <td>{{ date("m-d-Y", strtotime($payment['date'])) }}</td>
                                <td>{{ ucfirst($payment['status']) }}</td>
                                <td>{{ ucfirst($payment['payment_status']) }}</td>
                            </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="12">
                            <div class="alert alert-danger" role="alert"> No payment history found. </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection
@section('js')
<script>
/**
     * Get invoice detail.
     * @request invoiceId
     * @response object.
     */
    function getInvoiceDetail(data, type = 'invoice') {
        var url = "{{route('common.paymentInvoiceDetail', ['invoiceId'=>'%recordId%'])}}";
        url = url.replace('%recordId%', data.stripe_invoice_id);
        $.ajax({
            type: "GET",
            url: url,
            data: {},
            success: function(response) {
                if (response.success && response.data && response.data.hosted_invoice_url) {
                    setTimeout(function() {
                        if (type == 'refund') {
                            showRefundModal(response.data, data);
                        } else {
                            window.open(response.data.hosted_invoice_url, '_blank', 'noopener, noreferrer');
                        }
                    }, 500)
                } else {
                    _toast.error('Somthing went wrong.');
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }

    function sendNotification(data) {
        var url = "{{route('common.notifyToUser', ['userId'=>'%userId%'])}}";
        url = url.replace('%userId%', data);
        $.ajax({
            type: "POST",
            url: url,
            data: {},
            success: function(response) {
                if (response.success && response.data) {
                    _toast.success(response.message);
                } else {
                    _toast.error('Somthing went wrong.');
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }
</script>
@endsection