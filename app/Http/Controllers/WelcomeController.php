<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SynchronisationController;

class WelcomeController extends Controller
{
    protected $synchronisationController;

    public function __construct(SynchronisationController $synchronisationController)
    {
        $this->synchronisationController = $synchronisationController;
    }

    public function index(Request $request)
    {
        // Appeler la méthode synchronize du SynchronisationController
        $this->synchronisationController->synchronize($request);

        return view('welcome');
    }
}