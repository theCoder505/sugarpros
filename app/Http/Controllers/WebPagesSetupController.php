<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Notification;
use App\Models\Reviews;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class WebPagesSetupController extends Controller
{






    public function faqManagement()
    {
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        return view('admin.faqs_management', compact('allFaqs'));
    }




    public function addNewFaq(Request $request)
    {
        Faq::insert([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->back()->with('success', 'FAQ Added Successfully!');
    }


    public function updaeFaq(Request $request)
    {
        $faqID = $request['id'];
        $question = $request['question'];
        $answer = $request['answer'];
        Faq::where('id', $faqID)->update([
            'question' => $question,
            'answer' => $answer,
        ]);

        return redirect()->back()->with('info', 'FAQ Updated Successfully!');
    }


    public function deleteFaq($faqID)
    {
        Faq::where('id', $faqID)->delete();
        return redirect()->back()->with('info', 'FAQ Removed Successfully!');
    }







    public function reviewsManagement()
    {
        $reviews = Reviews::orderBy('id', 'DESC')->get();
        return view('admin.reviews_management', compact('reviews'));
    }




    public function updateReviewStatus($reviewID, $status)
    {
        $reviewerId = Reviews::where('id', $reviewID)->value('reviewed_by');


        if ($status == 'show') {
            $update = Reviews::where('id', $reviewID)->update([
                'status' => 1,
            ]);
        } else {
            $update = Reviews::where('id', $reviewID)->update([
                'status' => 0,
            ]);
        }

        if ($status == 'show') {
            Notification::insert([
                'user_id' => $reviewerId,
                'notification' => 'Your Review To Our Platform Is Approved Successfully!',
            ]);

            return redirect()->back()->with('success', 'Review Approved Successfully!');
        } else {
            Notification::insert([
                'user_id' => $reviewerId,
                'notification' => 'Your Review To Our Platform Is Denied!',
            ]);

            return redirect()->back()->with('success', 'Review Denied Successfully!');
        }
    }






    public function removeReview($reviewID)
    {
        $reviewerId = Reviews::where('id', $reviewID)->value('reviewed_by');
        $delete = Reviews::where('id', $reviewID)->delete();

        Notification::insert([
            'user_id' => $reviewerId,
            'notification' => 'Your Review To Our Platform Is Removed Being A Fake Review!',
        ]);
        return redirect()->back()->with('success', 'Review Removed Successfully!');
    }














    public function addNewService(Request $request)
    {
        $request->validate([
            'service_title' => 'required',
            'point_heading' => 'required|array',
            'point_shortnote' => 'required|array',
            'service_image' => 'required|image'
        ]);

        $service_title = $request->service_title;
        $point_heading = $request->point_heading;
        $point_shortnote = $request->point_shortnote;

        if ($request->hasFile('service_image')) {
            $file = $request->file('service_image');
            $extension = $file->getClientOriginalExtension();
            $filename = 'service_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'services/';
            $file->move(public_path($path), $filename);
            $service_image = $path . $filename;
        }

        $insert = Service::create([
            'service_image' => $service_image,
            'service_title' => $service_title,
            'service_points' => json_encode($point_heading),
            'service_point_details' => json_encode($point_shortnote),
        ]);

        return redirect()->back()->with('success', 'Service added successfully!');
    }











    public function servicesManagement()
    {
        $allServices = Service::orderBy('id', 'DESc')->get();
        return view('admin.services', compact('allServices'));
    }






    public function updateService(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_title' => 'required',
            'point_heading' => 'required|array',
            'point_shortnote' => 'required|array',
            'service_image' => 'sometimes|image'
        ]);

        $service_id = $request->service_id;
        $service_title = $request->service_title;
        $point_heading = $request->point_heading;
        $point_shortnote = $request->point_shortnote;

        $service = Service::findOrFail($service_id);

        if ($request->hasFile('service_image')) {
            // Delete old image if exists
            if ($service->service_image && file_exists(public_path($service->service_image))) {
                unlink(public_path($service->service_image));
            }

            $file = $request->file('service_image');
            $extension = $file->getClientOriginalExtension();
            $filename = 'service_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'services/';
            $file->move(public_path($path), $filename);
            $service_image = $path . $filename;
        } else {
            $service_image = $service->service_image;
        }

        $service->update([
            'service_image' => $service_image,
            'service_title' => $service_title,
            'service_points' => json_encode($point_heading),
            'service_point_details' => json_encode($point_shortnote),
        ]);

        return redirect()->back()->with('success', 'Service updated successfully!');
    }







    public function deleteService($serviceID)
    {
        $service = Service::findOrFail($serviceID);

        // Delete image file if exists
        if ($service->service_image && file_exists(public_path($service->service_image))) {
            unlink(public_path($service->service_image));
        }

        $service->delete();

        return redirect()->back()->with('success', 'Service removed successfully!');
    }
    //
}
