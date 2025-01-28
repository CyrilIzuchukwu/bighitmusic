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
</head>

<body>

    <div class="form-container">
        <div class="sidebar">
            <h3>Step 2</h3>
            <p>
                Provide your residential address, including house number, street name, city, state, and any other relevant details for accurate processing of your application.
            </p>
            <ul class="steps">
                <li class="active">
                    <span>1</span> Personal Information
                </li>
                <li class="active">
                    <span>2</span> Residential Details
                </li>
                <li>
                    <span>3</span> Additional Information
                </li>
            </ul>
        </div>

        <div class="main-form">
            <h2>Residential Details</h2>
            <p class="sub-text">
                Please provide your full residential address
            </p>

            @php
            $residentialDetails = session('residential_details', []);
            @endphp

            <form style="width: 100%;" class="row g-3" action="{{ route('form.saveResidential') }}" method="POST">
                <a href="{{ route('vacation.form') }}" class="previous-step">
                    <div class="back-icon">
                        <i class="ri-arrow-left-line"></i>
                    </div>
                    <span>Previous Step</span>
                </a>
                @csrf
                <div class="col-12">
                    <div class="form-floating mb-3">

                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $residentialDetails['address'] ?? '') }}">
                        <label for="address">Residential Address</label>
                        @error('address') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="country_of_residence" name="country_of_residence" value="{{ old('country_of_residence', $residentialDetails['country_of_residence'] ?? '') }}">
                        <label for="country_of_residence">Country of Residence</label>
                        @error('country_of_residence') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary submit-btn">Next Step <i class="ri-arrow-right-line"></i> </button>
                </div>
            </form>
        </div>
    </div>



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

</html>
