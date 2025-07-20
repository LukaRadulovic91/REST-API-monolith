<?php

namespace App\Http\Controllers\Web;

use App\Enums\JobAdStatus;
use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

/**
 * Class HomeController
 *
 * @package App\Http\Controllers\Web
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.jobAds.dashboard', [
            'statuses' => JobAdStatus::asSelectArray(),
            'positions' => Position::all()->pluck('title', 'id')->toArray()
        ]);
    }
}
