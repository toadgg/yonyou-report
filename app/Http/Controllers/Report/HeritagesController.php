<?php

namespace App\Http\Controllers\Report;

use App\Heritage;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HeritagesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {

        $target = $request->get('category', '全部');

        $total = Heritage::count();
        if ($target == '全部') {
            $heritages = Heritage::paginate(10);
        } else {
            $heritages = Heritage::where('category', $target)->paginate(10);
        }

        $categories = Heritage::groupBy('category')->select('category', DB::raw('count(*) as total'))->orderBy('total', 'desc')->get();

        return view('report.heritages', ['rows' => $heritages, 'categories' => $categories, 'target' => $target, 'total' => $total]);
    }

    public function export(Request $request){

    }
}
