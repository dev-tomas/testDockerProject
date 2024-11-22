<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProviderQuotation;
use App\Provider;
use App\Client;
use App\Requirement;
use Illuminate\Support\Str;

class UnLoginController extends Controller
{
    public function setQuotation(Request $request, $ruc, $requirement)
    {
        if ($request->hasValidSignature()) {
            $signed = 1;
            return view('logistic.purchase.setQuotation', compact('ruc', 'signed', 'requirement'));
        } else {
            return abort(401);
        }
    }

    public function store(Request $request)
    {
        if ($request->signed == 1) {
            $now = new \DateTime();
            $ruc = $request->ruc;
            $providerDoc = $request->provider;
            $serie = Str::before($request->requirement, '-');
            $correlative = Str::after($request->requirement, '-');

            $client = Client::where('document', $ruc)->first();

            $provider = Provider::where('document', $providerDoc)->where('client_id', $client->id)->first();
            $requirement = Requirement::where('serie', $serie)->where('correlative', $correlative)->where('client_id', $client->id)->first();

            if ($provider != null) {
                $file = $request->file('filed');
                $extension = $file->getClientOriginalExtension();
                $fileName = $providerDoc . ' - ' . $client->id . ' - ' . $now->format('d-m-Y') . '.' . $extension;
                $path = public_path('purchases/quotations/pdf/'.$client->id);
                $file->move($path, $fileName);

                $quotation = new ProviderQuotation;
                $quotation->provider_id = $provider->id;
                $quotation->client_id = $client->id;
                $quotation->requirement_id = $requirement->id;
                $quotation->file = 'purchases/quotations/pdf/'.$client->id .'/'.$fileName;
                $quotation->save();

                toastr()->success('Se envió satisfactoriamente su cotizacion');

                return redirect()->route('finish');
            } else {
                toastr()->error('Proveedor no registrado');

                return redirect()->back();
            }
        } else {
            // 
            return abort(401);;
        }
    }

    public function finish()
    {
        return view('logistic.purchase.finishSetQuotation');
    }
}
