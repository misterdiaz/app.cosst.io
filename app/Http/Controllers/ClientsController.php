<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientsController extends Controller
{
    //

    public function select(){
        $clients = \App\Client::all();
        return view('clients.select', compact('clients'));
    }

    public function select_store(Request $request){
        $this->validate($request, [
            'client_id'=>'required'
        ]);
        $client_id = $request->get('client_id');
        $client = \App\Client::find($client_id);
        flash("Client set successfully. Now working with client: $client->name")->success();
        $request->session()->put('working_client.id', $client_id);
        $request->session()->put('working_client.name', $client->name);
        $request->session()->put('working_client.client_domain', $client->client_domain);
        $request->session()->put('working_client.slack_url', $client->slack_url);
        $request->session()->put('working_client.form1_url', $client->form1_url);
        $request->session()->put('working_client.form2_url', $client->form2_url);
        $request->session()->put('working_client.google_drive_folder', $client->google_drive_folder);
        return redirect("/");
    }
}