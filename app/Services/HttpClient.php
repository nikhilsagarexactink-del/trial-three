<?php

 namespace App\Services;  
 use GuzzleHttp\Client; 
 use Exception;
 
class HttpClient
{
    
    public static function post($api, $myBody)
    {
        try {
            $client = new Client();
            $secureToken = env('BREHM_SECURE_TOKEN');
            $apiUrl = env('BREHM_API_URL');
            $myBody['secure_token'] = $secureToken; 
            $url = $apiUrl.$api;
            $response = $client->request('POST', $url,  ['form_params'=>$myBody]); 
            if($response->getStatusCode()==200){
               $response = json_decode((string) $response->getBody()); 
               return $response;
            } else {  
               throw new Exception('Somthing went wrong.', 1);
            }            
        } catch (\GuzzleHttp\Exception\RequestException $ex) { 
          if ($ex->hasResponse()) {
                throw $ex;
                // $response = $ex->getResponse();
                // $resBody = json_decode((string) $response->getBody());  
                // throw new Exception($resBody->message, 1);
            } else {
                $response = $ex->getHandlerContext(); 
                // if (isset($response['error'])) {
                //     return $response['error'];
                // }
                throw $response;
            } 
        }
    }
      
    public static function get($api, $myBody=[])
    {
        try {
            $client = new Client();
            $secureToken = env('BREHM_SECURE_TOKEN');
            $apiUrl = env('BREHM_API_URL');
            $myBody['secure_token'] = $secureToken; 
            $url = $apiUrl.$api;
            $response = $client->request('GET', $url); 
            $response = json_decode((string) $response->getBody()); 
            return $response->data;
        } catch (\GuzzleHttp\Exception\RequestException $ex) { 
          if ($ex->hasResponse()) {
                $response = $ex->getResponse();
                $resBody = json_decode((string) $response->getBody());  
                throw new Exception($resBody->message, 1);
            } else {
                $response = $ex->getHandlerContext(); 
                // if (isset($response['error'])) {
                //     return $response['error'];
                // }
                throw $response;
            } 
        }
    }
}
