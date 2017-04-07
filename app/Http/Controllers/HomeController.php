<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rows = DB::connection('sqlsrv')->table('TY_XXWHZJKS')
            ->join('TY_XINXIWEIHU', 'TY_XINXIWEIHU.ty_xinxiid', '=', 'TY_XXWHZJKS.ty_xinxiid')
            ->where('TY_XXWHZJKS.keshangname', '!=', '')
            ->select('TY_XXWHZJKS.*', 'TY_XINXIWEIHU.name', 'TY_XINXIWEIHU.dept', 'TY_XINXIWEIHU.time', 'TY_XINXIWEIHU.nc', 'TY_XINXIWEIHU.shuoming')
            ->get();
        dd($rows->forPage(2, 10));
        return redirect(route('report.xyjh'));
//        return view('home');
    }
}
