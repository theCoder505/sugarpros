<?php

namespace App\Http\Controllers;

use App\Models\BillerAdmin;
use Illuminate\Http\Request;

class BillerAdminController extends Controller
{






    public function billerAdmins()
    {
        $all_biller_admins = BillerAdmin::orderBy('id', 'DESC')->get();
        return view('admin.biller_admins', compact('all_biller_admins'));
    }





    public function addNewBillerAdmin(Request $request)
    {
        $biller_name = $request['biller_name'];
        $biller_email = $request['biller_email'];
        $password = $request['password'];
        $randomNumber = str_pad(mt_rand(1111, 9999), 4, '0', STR_PAD_LEFT);
        $biller_admin_id = 'Biller-' . $randomNumber;

        BillerAdmin::create([
            'biller_admin_id' => $biller_admin_id,
            'biller_name' => $biller_name,
            'biller_email' => $biller_email,
            'password' => $password,
        ]);

        return redirect()->back()->with('success', 'Biller Admin Created Successfully!');
    }





    public function updateBillerData(Request $request)
    {
        $biller_admin_id = $request['biller_admin_id'];
        $biller_name = $request['biller_name'];
        $biller_email = $request['biller_email'];
        $password = $request['password'];

        BillerAdmin::where('biller_admin_id', $biller_admin_id)->update([
            'biller_name' => $biller_name,
            'biller_email' => $biller_email,
            'password' => $password,
        ]);

        return redirect()->back()->with('info', 'Biller Admin Updated Successfully!');
    }


    public function removeBiller($biller_admin_id)
    {
        $remove = BillerAdmin::where('biller_admin_id', $biller_admin_id)->delete();
        return redirect()->back()->with('success', 'Biller Admin ID: ' . $biller_admin_id . ' Removed Successfully!');
    }





    //
}
