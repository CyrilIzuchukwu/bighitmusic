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
            <h3>Step 1</h3>
            <p>
                Provide your full name, date of birth, gender, and other relevant details
                for accurate identification and processing of your application.
            </p>
            <ul class="steps">
                <li class="active">
                    <span>1</span> Personal Information
                </li>
                <li>
                    <span>2</span> Residential Details
                </li>
                <li>
                    <span>3</span> Additional Information
                </li>
            </ul>
        </div>

        <div class="main-form">

            <h2>Personal Information</h2>
            <p class="sub-text">
                Please provide your personal details and any other relevant personal
                information. This information is essential for identifying you and
                processing your application accurately.
            </p>
            @php
            $personalDetails = session('personal_details', []);
            @endphp

            <form class="row g-3" action="{{ route('form.personal') }}" method="POST">
                @csrf
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" name="email" value="{{ old('email', $personalDetails['email'] ?? '') }}">
                        <label for="floatingInput">Email</label>
                        @error('email') <span style="display: block" class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $personalDetails['first_name'] ?? '') }}">
                        <label for="first_name">First Name</label>
                        @error('first_name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $personalDetails['last_name'] ?? '') }}">
                        <label for="last_name">Last Name</label>
                        @error('last_name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                </div>
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $personalDetails['phone'] ?? '') }}">
                        <label for="phone">Mobile Number</label>
                        @error('phone') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation', $personalDetails['occupation'] ?? '') }}">
                        <label for="occupation">Occupation</label>
                        @error('occupation') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <select id="gender" name="gender" class="form-select">
                        <option selected disabled>Select Gender</option>
                        <option value="Male" {{ old('gender', $personalDetails['gender'] ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $personalDetails['gender'] ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $personalDetails['gender'] ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <span class="error-message">{{ $message }}</span> @enderror

                </div>


                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control flatpickr-input" id="dob" name="dob" data-provider="flatpickr" data-date-format="d F Y" readonly="readonly" value="{{ old('dob', $personalDetails['dob'] ?? '') }}">
                        <label for="dob">Date of Birth</label>
                        @error('dob') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary submit-btn">Next Step <i class="ri-arrow-right-line"></i> </button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#dob", {
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

</html>
