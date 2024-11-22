<?php

namespace App\Http\Controllers;

use App\Client;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
{
    public function convertAndCertificate(Request $request)
    {
        try {
            $file = $request->file('certificate');
            $extension = $file->getClientOriginalExtension();
            $password = $request->post('password_certificate');

            if ($extension == 'pfx') {
                $pfx = file_get_contents($file);
                $certificate = new X509Certificate($pfx, $password);
                $pem = $certificate->export(x509ContentType::PEM);
            } else {
                $results = array(); 
                $worked = openssl_pkcs12_read(file_get_contents($file), $results, $password); 
                if($worked) { 
                    $ext = null;
                    if (isset($results['pkey'])) {
                        $ext = $results['pkey'];
                    }
                    if (isset($results['cert'])) {
                        $cert = null;
                        openssl_x509_export($results['cert'], $cert);   
                    }
                    $pem = $cert . $ext;
                } 
            }
            
            $name = Auth::user()->headquarter->client->document . '.pem';

            Storage::disk('public')->put($name, $pem);

            $user = Client::find(Auth::user()->headquarter->client_id);
            $user->certificate = $name;
            $user->expiration_certificate = date('Y-m-d', strtotime($request->expiration));
            $user->save();

            return Redirect::back()->with('success','Certificado subida satisfactoriamente!');
        } catch (\Exception $e) {
            return Redirect::back()->with('error','Ocurrió un error al intentar subir su certificado!');
        }
    }
}
