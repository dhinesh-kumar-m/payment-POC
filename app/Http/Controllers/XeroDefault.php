<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LangleyFoxall\XeroLaravel\OAuth2;
use League\OAuth2\Client\Token\AccessToken;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Api\AccountingApi;
use GuzzleHttp\Client;
use DateTime;
use XeroAPI\XeroPHP\Models\Accounting\Contact;
use XeroAPI\XeroPHP\Models\Accounting\Contacts;
use XeroAPI\XeroPHP\Models\Accounting\LineItemTracking;
use XeroAPI\XeroPHP\Models\Accounting\LineItem;
use XeroAPI\XeroPHP\Models\Accounting\Invoice;
use XeroAPI\XeroPHP\Models\Accounting\Invoices;
use XeroAPI\XeroPHP\Models\Accounting\Phone;
use XeroAPI\XeroPHP\Models\Accounting\TrackingCategory;
use App\Models\UserMaster;
use App\Models\CartOfAccountXero;
use Illuminate\Support\Facades\Log;
use XeroAPI\XeroPHP\Models\Accounting\Account;
use XeroAPI\XeroPHP\Models\Accounting\AccountType;

class XeroDefault extends Controller
{
    public function create_Account(Request $request){
        $user = auth()->user();
        $accessToken= json_decode($user->xero_access_token,true);
        $tenantId= $user->tenant_id;
        // dd($accessToken);

        $config = Configuration::getDefaultConfiguration()->setAccessToken( $accessToken["access_token"] );      

        $apiInstance = new AccountingApi(
            new Client(),
            $config
        );
        $xeroTenantId = $tenantId;
        $default_data= CartOfAccountXero::get();
        foreach($default_data as $datas)
        {
            // Log::info('codes');
            // Log::info($datas->code);
            $account = new Account;
            $account->setCode($datas->code);
            $account->setName($datas->name);
            $account->setType($datas->type);
            $account->setDescription($datas->description);

        
            try {
                $result = $apiInstance->createAccount($xeroTenantId, $account);
        
            } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->createAccount: ', $e->getMessage(), PHP_EOL;
            }
        }

        return $this->successResponse($result=null, "Account created Successfully", 200);
    }

    
}
