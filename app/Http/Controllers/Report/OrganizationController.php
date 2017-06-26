<?php
/**
 * Created by PhpStorm.
 * User: zhangxl
 * Date: 2017/6/1
 * Time: 上午10:23
 */

namespace App\Http\Controllers\Report;

use App\Contact;
use App\Deptdoc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;


class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function _createBuilder() {
        $_builder = DB::connection('oracle')->table('bd_psndoc')
            ->leftJoin('bd_deptdoc', 'bd_psndoc.PK_DEPTDOC', '=', 'bd_deptdoc.PK_DEPTDOC')
            ->select('bd_psndoc.PSNNAME', 'bd_psndoc.PSNCODE', 'bd_psndoc.PK_PSNDOC', 'bd_deptdoc.DEPTNAME', 'bd_deptdoc.DEPTCODE', 'bd_psndoc.PK_DEPTDOC')
            ->where('bd_psndoc.PK_CORP', '1002')
            ->where('bd_deptdoc.DEPTCODE', '!=','998')
            ->orderBy('bd_deptdoc.DEPTCODE')
            ->orderBy('bd_psndoc.PSNCODE');
        return $_builder;
    }

    public function index(Request $request) {

        $contacts = Contact::all();

        $builder = $this->_createBuilder();
        $dept = $builder->get()->groupBy('deptname');
        return view('report.organization', compact('dept', 'contacts'));
    }

    public function syncDept() {
        $depts = Deptdoc::all()->groupBy('deptname');
        $contacts = Contact::all();
        foreach ($contacts as $member) {

            if (!array_key_exists($member['deptname'], $depts->toArray())) {
                $member->deptcode = 'no found';
            } else {

                $targetDepts = $depts[$member['deptname']];
                if (count($targetDepts) == 1) {
                    $member->deptcode = $targetDepts[0]['deptcode'];
                    $member->pk_deptdoc = $targetDepts[0]['pk_deptdoc'];
                }
                if (count($targetDepts) > 1 ) {
                    $member->deptcode = 'dup ' . count($targetDepts) . ' times';
                }

            };

            $member->save();

        }

        return redirect(route('report.organization'));
    }

    public function syncPsn()
    {

        $builder = $this->_createBuilder();
        $psns = $builder->get();

        $contacts = Contact::all();

        $lastDept = '';
        $currcySeq = 0;
        foreach ($contacts as $member) {
            if ($lastDept != $member->pk_deptdoc) {
                $lastDept = $member->pk_deptdoc;
                $currcySeq = 0;
            };
            $filter = $psns->where('pk_deptdoc', $member->pk_deptdoc)
                            ->where('psnname', $member->psnname);
            if(count($filter) == 0) {
                $member->psncode = $member->sync_target == 1 ? 'no found' : 'ignore' ;
            } elseif (count($filter) == 1) {
                $member->pk_psndoc = $filter->first()->pk_psndoc;
                $member->psncode = $filter->first()->psncode;
            } else {
                $member->psncode = 'dup ' . count($filter) . ' times';
            }
            $member->psncode = $member->deptcode . sprintf("%03d", ++$currcySeq);
            $member->save();
        }

        return redirect(route('report.organization'));
    }

    public function generateSeq() {
        $contacts = Contact::all();
        foreach ($contacts as $member) {
            if ($member->sync_target == 1 && $member->pk_psndoc) {
                print_r("UPDATE bd_psndoc set psncode = '$member->psncode' where PK_CORP='1002' and PK_DEPTDOC='$member->pk_deptdoc' and PK_PSNDOC='$member->pk_psndoc';<br/>");
            }
        }
    }

    public function generateClean() {
        $contactIds = collect(Contact::whereNotNull('pk_psndoc')->get(['pk_psndoc'])->toArray())->flatten()->all();
        $shouldClean = DB::connection('oracle')->table('bd_psndoc')
            ->leftJoin('bd_deptdoc', 'bd_psndoc.PK_DEPTDOC', '=', 'bd_deptdoc.PK_DEPTDOC')
            ->select('bd_psndoc.PSNNAME', 'bd_psndoc.PSNCODE', 'bd_psndoc.PK_PSNDOC', 'bd_deptdoc.DEPTNAME', 'bd_deptdoc.DEPTCODE', 'bd_psndoc.PK_DEPTDOC')
            ->where('bd_psndoc.PK_CORP', '1002')
            ->whereNotIn('bd_deptdoc.DEPTCODE', ['990','991','998','999'])
            ->whereNotIn('bd_psndoc.pk_psndoc', $contactIds)->get();
        foreach ($shouldClean as $member) {
            print_r("update bd_psndoc set SEALDATE='2017-06-07', PK_DEPTDOC='1002F910000000144A60' where PK_PSNDOC='$member->pk_psndoc';<br/>");
        }
    }

    public function generateSeqRm() {
        $seqRm = DB::connection('oracle')->table('bd_psndoc')
            ->leftJoin('bd_deptdoc', 'bd_psndoc.PK_DEPTDOC', '=', 'bd_deptdoc.PK_DEPTDOC')
            ->select('bd_psndoc.PSNNAME', 'bd_psndoc.PSNCODE', 'bd_psndoc.PK_PSNDOC', 'bd_deptdoc.DEPTNAME', 'bd_deptdoc.DEPTCODE', 'bd_psndoc.PK_DEPTDOC')
            ->where('bd_psndoc.PK_CORP', '1002')
            ->where('bd_deptdoc.DEPTCODE', '998')
            ->orderBy('bd_psndoc.PSNNAME')
            ->orderBy('bd_psndoc.SEALDATE')->get();
        
        $currcySeq = 0;
        foreach ($seqRm as $member) {
            $newCode = $member->deptcode . sprintf("%03d", ++$currcySeq);
            print_r("UPDATE bd_psndoc set psncode = '$newCode' where PK_CORP='1002' and PK_DEPTDOC='$member->pk_deptdoc' and PK_PSNDOC='$member->pk_psndoc';<br/>");
        }

    }

}