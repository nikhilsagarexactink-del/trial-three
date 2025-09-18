<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\PlanRepository;
use App\Repositories\AffiliateRepository;

class HomeController extends Controller
{
    public function index()
    {
        return redirect(route('userLogin'));
    }

    public function planIndex(Request $request)
    {
        try{
            $groupCode = $request->query('group_code');
            validateGroupCode($groupCode);
            // This will automatically abort with 404 if group doesn't exist or is deleted
            if($request->has('refrel_code')){
                $refrelCode = $request->query('refrel_code') ?? null;
                validateRefrelCode($refrelCode);
            }

            if($request->has('user_type') && $request->get('user_type') == 'athlete'){
                $plans = PlanRepository::findAll([['status', 'active'], ['visibility', 'active']]);
                $affiliateSetting = AffiliateRepository::getSettings(['plan_type']);

                return view('plan', compact('plans', 'affiliateSetting'));
            }else{
                abort(404);
            }
        }catch(\Exception $ex){
            abort(404);
        }
    }

    /**
     * Show the landing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function landingIndex(){
        return view('landing-page');
    }
}
