@extends('layouts.app')
@section('head')
<title>Quote | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php
$id = request()->route('id');
$userType = userType();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.quote', ['user_type'=>$userType])}}">Quote</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Quote
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.updateQuote',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Author</label>
                                <input type="text" class="form-control" value="{{$result->author}}" placeholder="Author" name="author">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Type<span class="text-danger">*</span></label>
                                <div class="select-arrow">
                                    <select class="form-control" name="quote_type">
                                        <option value="">Select Type</option>
                                        <option value="quote" {{$result->quote_type=='quote' ? 'selected' : ''}}>Quote</option>
                                        <option value="message" {{$result->quote_type=='message' ? 'selected' : ''}}>Message</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description<span class="text-danger">*</span></label>
                                <textarea class="form-control" placeholder="Description" name="description">{{$result->description}}</textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateQuote()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.quote', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\QuoteRequest','#updateForm') !!}

<script>
    /**
     * Update Record.
     * @request form fields
     * @response object.
     */
    function updateQuote() {
        var formData = $("#updateForm").serializeArray();
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.updateQuote', ['id'=>'%recordId%'])}}";
            url = url.replace('%recordId%', "{{$result['id']}}");
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href = "{{route('user.quote', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Quote not updated.');
                    }

                },
            });
        }
    };
</script>
@endsection