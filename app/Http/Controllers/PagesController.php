<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;
use Session;

class PagesController extends Controller
{
    public function getForm() {
    	return view('form');
    }

    public function postForm(Request $request) {
    	$name = $request->name;
    	$gender = $request->gender;

        // grab google recaptcha token
    	$token = $request->input('g-recaptcha-response');

    	if ($token) {
            # initiate Guzzle for recaptcha server-side API
            $client = new Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                    'form_params' => array(
                        'secret' => '6LcYIQgUAAAAAFLkcmYAQVmnMgzLe2rHbdgCLsoW',
                        'response' => $token
                        )
                ]);
            $results = json_decode($response->getBody()->getContents());

            # https://developers.google.com/recaptcha/docs/verify
            if ($results->success) {
                # dd($results);

                Session::flash('success', 'Yes we know you are human!');

                # we know it was submitted
                return view('name')->withName($name)->withGender($gender);
            } else {
                # $results->error_codes
                Session::flash('error', 'You are probably a robot!');

                return redirect('/');
            }

    	} else {
    		return redirect('/');
    	}

    	
    }
}
