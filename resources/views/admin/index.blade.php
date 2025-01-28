@extends('layouts.app')
@section('content')
<!-- row -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6">
                    <div class="swiper">
                        <div class="swiper-wrapper " aria-live="polite">
                            <div class="swiper-slide " role="group" style="width: 100%; margin-right: 30px;">
                                <div class="card coin-card bg-secondary">
                                    <div class="back-image">
                                        <svg width="121" height="221" viewBox="0 0 121 221" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="135.5" cy="84.5" r="40" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="135.5" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="109.5" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="86.5" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="64.5" stroke="#BE7CFF"></circle>
                                        </svg>
                                    </div>
                                    <div class="card-body p-4 ">
                                        <div class="title">
                                            <h4>Registered Mails</h4>
                                        </div>
                                        <div class="chart-num">
                                            <h2>{{ $mailCount }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /row -->
                </div>

                <div class="col-md-6">
                    <div class="swiper">
                        <div class="swiper-wrapper " aria-live="polite">
                            <div class="swiper-slide " role="group" style="width: 100%; margin-right: 30px;">
                                <div class="card coin-card bg-secondary">
                                    <div class="back-image">
                                        <svg width="121" height="221" viewBox="0 0 121 221" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="135.5" cy="84.5" r="40" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="135.5" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="109.5" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="86.5" stroke="#BE7CFF"></circle>
                                            <circle cx="136" cy="85" r="64.5" stroke="#BE7CFF"></circle>
                                        </svg>
                                    </div>
                                    <div class="card-body p-4 ">
                                        <div class="title">
                                            <h4>Number of Feedbacks</h4>
                                        </div>
                                        <div class="chart-num">
                                            <h2>{{ $feedbackCount }}</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                    <!-- /row -->
                </div>



            </div>

            @php
            $hour = now()->format('H');
            if ($hour < 12 ) {
                $greeting='Good morning' ;
                } elseif ($hour < 16) {
                $greeting='Good afternoon' ;
                } else {
                $greeting='Good evening' ;
                }
                @endphp

                <div class="row">
                <div class="col-xl-12">
                    <div class="card bubles">
                        <div class="card-body">
                            <div class="buy-coin  bubles-down">
                                <div>
                                    <h2>{{ ucfirst($greeting) }}, {{ ucfirst(Auth::user()->name) }}!</h2>

                                    <a href="{{ route('add_mail') }}" class="btn btn-primary mt-3">Add Mail</a>
                                </div>
                                <div class="coin-img">
                                    <img src="images/coin.png" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- start  -->

        </div>
    </div>
</div>

</div>
@endsection
