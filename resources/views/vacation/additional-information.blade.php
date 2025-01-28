<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bighit Music Private Vacation Form</title>
    <link rel="stylesheet" href="{{ asset('xhtml/css/vacation.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
        rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>

<body>

    <div class="form-container">
        <div class="sidebar">
            <h3>Step 3</h3>
            <p>
                Provide any additional information, including details relevant to your application, to ensure accurate processing and consideration.
            </p>
            <ul class="steps">
                <li class="active">
                    <span>1</span> Personal Information
                </li>
                <li class="active">
                    <span>2</span> Residential Details
                </li>
                <li class="active">
                    <span>3</span> Additional Information
                </li>
            </ul>
        </div>

        <div class="main-form">

            <a href="{{ route('form.residential') }}" class="previous-step">
                <div class="back-icon">
                    <i class="ri-arrow-left-line"></i>
                </div>
                <span>Previous Step</span>
            </a>

            <h2>Additional information</h2>
            <p class="sub-text">
                Please provide any additional information or notes.
            </p>

            @php
            $additionalInformation = session('additional_information', []);
            @endphp

            <form class="row g-3" action="{{ route('form.saveAdditionalInformation') }}" method="POST">
                @csrf
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="meeting_point" name="meeting_point" value="{{ old('meeting_point', $additionalInformation['meeting_point'] ?? '') }}">
                        <label for="meeting_point">Preferred meeting point with the celebrity</label>
                        @error('meeting_point') <span class="error-message">{{ $message }}</span> @enderror
                        <span class="information">
                            *Please specify your preferred meeting point. This information will help us arrange a convenient and suitable place for your meeting. 
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="airport" name="airport" value="{{ old('airport', $additionalInformation['airport'] ?? '') }}">
                        <label for="airport">Airport close to you</label>
                        @error('airport') <span class="error-message">{{ $message }}</span> @enderror
                        <p class="information">
                            Please provide the name and city of the airport closest to your location. This information will help us in making travel arrangements or providing relevant logistical support. 
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control flatpickr-input" id="vacation_date" name="vacation_date" data-provider="flatpickr" data-date-format="d F Y" readonly="readonly" value="{{ old('vacation_date', $additionalInformation['vacation_date'] ?? '') }}">
                        <label for="vacation_date">Vacation Date</label>
                        @error('vacation_date') <span class="error-message">{{ $message }}</span> @enderror

                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                </div>


                <div class="attention">
                    <h2>Attention</h2>
                    <p>This vacation entails a financial commitment. We appreciate your interest in Hybe.</p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#vacation_date", {
                dateFormat: "d F Y", // Matches your desired format
                altInput: true, // Optional: To display a more readable format
                altFormat: "F j, Y", // Optional: If altInput is true
                defaultDate: "today", // Optional: Default to today's date
            });
        });
    </script>


    <!-- sweet alert  -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @if (session('message'))
    <script>
        swal("Successful!", "{{ session('message') }}!", "success");
    </script>
    @endif
    @if (session('error'))
    <script>
        swal("Error!", "{{ session('error') }}!", "warning");
    </script>
    @endif
    @if (Session::has('success'))
    <script>
        swal("Successful!", "{{ Session::get('success') }}!", "success");
    </script>
    @endif

    @if (Session::has('error'))
    <script>
        swal("Warning!", "{{ Session::get('error') }}!", "warning");
    </script>
    @endif
</body>

<style>
    .information {
        color: #F10C0C;
        font-family: Onest;
        font-size: 10px;
        font-weight: 400;
        line-height: 15.3px;
        text-align: left;
    }
</style>

</html>
