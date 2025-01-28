<?php

namespace App\Http\Controllers;

use App\Models\PrivateVacation;
use App\Models\VacationForm;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class VacationController extends Controller
{
    //

    public function showForm(Request $request)
    {
        return view('vacation.personal-details');
    }


    public function savePersonalDetails(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'occupation' => 'required|string|max:255',
            'gender' => 'required|string|in:Male,Female,Other',
            'dob' => 'required|date',
        ]);

        // Save data to session
        session(['personal_details' => $validatedData]);

        // dd(session('personal_details'));

        // Redirect to the next step
        return redirect()->route('form.residential');
    }


    public function showResidentialForm()
    {
        return view('vacation.residential-details');
    }


    public function saveResidentialDetails(Request $request)
    {
        $validatedData = $request->validate([
            'address' => 'required|string|max:255',
            'country_of_residence' => 'required|string|max:255',
        ]);

        // Save data to session
        session(['residential_details' => $validatedData]);

        // Redirect to the next step
        return redirect()->route('additional.information');
    }



    public function showAdditionalInformation()
    {
        return view('vacation.additional-information');
    }


    public function saveAdditionalInformation(Request $request)
    {
        // Validate the form input data
        $validatedData = $request->validate([
            'meeting_point' => 'required|string|max:255',
            'airport' => 'required|string|max:255',
            'vacation_date' => 'required|date',
        ]);


        session(['additional_information' => $validatedData]);


        // database
        $personalDetails = session('personal_details');
        $residentialDetails = session('residential_details');
        $additionalInformation = session('additional_information');

        // Format the date of birth using Carbon to ensure it's in the correct format
        $dob = Carbon::parse($personalDetails['dob'])->format('Y-m-d');

        // Format the vacation date to the correct format as well
        $vacationDate = Carbon::parse($additionalInformation['vacation_date'])->format('Y-m-d');



        // Create and save the data to the database
        $vacationForm = new VacationForm();
        $vacationForm->email = $personalDetails['email'];
        $vacationForm->first_name = $personalDetails['first_name'];
        $vacationForm->last_name = $personalDetails['last_name'];
        $vacationForm->phone = $personalDetails['phone'];
        $vacationForm->occupation = $personalDetails['occupation'];
        $vacationForm->gender = $personalDetails['gender'];
        $vacationForm->dob = $dob;
        $vacationForm->address = $residentialDetails['address'];
        $vacationForm->country_of_residence = $residentialDetails['country_of_residence'];
        $vacationForm->meeting_point = $additionalInformation['meeting_point'];
        $vacationForm->airport = $additionalInformation['airport'];
        $vacationForm->vacation_date = $vacationDate;

        // Save the form to the database
        $vacationForm->save();

        // Clear all session data
        session()->flush();

        // Redirect back to the vacation form route with a success message
        return redirect()->route('vacation.form')->with('message', 'Submitted Successfully');
    }

    

    public function feedbacks()
    {
        $feedbacks = VacationForm::orderBy('created_at', 'desc')->get();
        return view('admin.mail.feedbacks', compact('feedbacks'));
    }


    public function delete_feedback($id)
    {
        $data = VacationForm::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', 'Feedback Deleted');
    }


    public function feedback_details($id)
    {
        $feedback = VacationForm::findOrFail($id);
        return view('admin.mail.feedback-details', compact('feedback'));
    }
}
