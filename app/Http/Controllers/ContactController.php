<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('Contact form data:', $request->all());

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to be logged in to send a message.');
        }

        try {
            // Validate the message content only
            $validated = $request->validate([
                'message' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        // Get the authenticated user's email
        $userEmail = Auth::user()->email;

        // Send the email using the current user's email
        Mail::to('ouendeufranck@gmail.com')->send(new ContactMail($validated, $userEmail));

        return redirect()->back()->with('success', 'Your message has been sent!');
    }
}
