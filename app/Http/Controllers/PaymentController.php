<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        return view('payment-form');
    }

    public function confirmPayment(Request $request)
    {
        $host = $request->getHttpHost();
        if($host == 'localhost')
        {
             # local key
             $working_key = 'E96F1BAD9A32CB6A3300A91B36A3F61F'; // Local Shared by CCAVENUES
             $access_code = 'AVEA05KG07BU29AEUB'; // Local Shared by CCAVENUES
        }else
        {
            # local key
            $working_key = 'CAC7F5DD57D0D2F871CE17F40C006EF0'; // Local Shared by CCAVENUES
            $access_code = 'AVEA05KG07BU28AEUB'; // Local Shared by CCAVENUES
        }
        
        $order_id = 'ORD' . rand(1111, 9999) . time();
        $merchant_data = '';
        $PaymenData = [
                                'merchant_id'  => $request->merchant_id ?? '',
                                'amount'       => $request->amount ?? '',
                                'currency'     => $request->currency ?? '',
                                'language'     => $request->language ?? '',
                                'redirect_url' => $request->redirect_url ?? '',
                                'cancel_url'   => $request->cancel_url ?? '',
                                'order_id'     => $order_id ?? '',
                                '_token'       => $request->_token ?? '',
                                'billing_email'    => $request->user_email ?? '',
                                'billing_tel'      => $request->mobile ?? '',
                                'billing_name'     => $request->user_name ?? '',
                                'delivery_country' => 'India',
                            ];
        foreach ($PaymenData as $key => $value) 
        {
            $merchant_data .= $key . '=' . urlencode($value) . '&';
        }

        $response   = $this->encrypt($merchant_data,$working_key);

        $encrypted_data = $response ?? '';

        return view('cc_avenue_payment')->with(['encrypted_data' => $encrypted_data,'access_code' =>$access_code]);
    }

    public function paymentWithCCAvenue(Request $request)
    {
        $host = $request->getHttpHost();
        if($host == 'localhost')
        {
             # local key
             $workingKey    = 'E96F1BAD9A32CB6A3300A91B36A3F61F'; // Working Key should be provided 
            
        }else
        {
            # local key
            $workingKey = 'CAC7F5DD57D0D2F871CE17F40C006EF0'; // Local Shared by CCAVENUES
            
        }

       
       $encResponse   = $request->encResp;  //This is the response sent by the CCAvenue Server
       $rcvdString    = $this->decrypt($encResponse, $workingKey); //Crypto Decryption used as per 
       $decryptValues = explode('&', $rcvdString);
       $dataSize      = sizeof($decryptValues);
       $order_status  = "";
       for($i = 0; $i < $dataSize; $i++) 
        {
            $information = explode('=',$decryptValues[$i]);
            if($i==3)   $order_status = $information[1];
        }
        $responseData = [];
        foreach ($decryptValues as $key => $value) 
        {
            for($i = 0; $i < $dataSize; $i++) 
            {
                $information = explode('=',$value);
            }
                $data =[$information[0] => $information[1] ?? ''];
                array_push($responseData,$data);
        }
        $finalData = [];
        for($i = 0; $i < $dataSize; $i++) 
        {
            $information = explode('=',$decryptValues[$i]);

            $finalData[$information[0]] = $information[1];

        }
        if($order_status === "Success")
        {
            toastr()->success('Payment successfully Done');            
            return redirect()->route('home');

        }else if($order_status==="Aborted")
        {
            $data =  "Your transaction has been Aborted.";
            toastr()->error($data); 
            return redirect()->route('home');
        }
        else if($order_status==="Failure")
        {
            $data = "Your transaction has been declined.";
            toastr()->error($data); 
            return redirect()->route('home');
        }
        else
        {
            $data = "Your transaction has been cancel.";
            toastr()->error($data); 
            return redirect()->route('home');
        
        }
       // dd($order_id,$encResponse,$rcvdString,$decryptValues,$dataSize,$responseData,$finalData);
        
    }


    function encrypt($plainText,$key)
    {
        $key = hex2bin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }
    function decrypt($encryptedText,$key)
    {
        $key = hex2bin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = hex2bin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }
}
