<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FaceController extends Controller
{
    public function index()
    {
        return view('profile.face');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'descriptor' => 'required',
            'photo' => 'required'
        ]);


        // simpan descriptor wajah
        $user->face_descriptor = json_encode(
            $request->descriptor
        );


        // simpan foto wajah
        $image = $request->photo;


        $image = str_replace(
            'data:image/png;base64,',
            '',
            $image
        );


        $image = str_replace(' ', '+', $image);


        $filename = 'faces/' . $user->id . '.png';


        Storage::disk('public')
            ->put(
                $filename,
                base64_decode($image)
            );


        $user->face_photo = $filename;

        $user->save();


        return response()->json([
            'success' => true,
            'message' => 'Wajah berhasil disimpan'
        ]);
    }
}