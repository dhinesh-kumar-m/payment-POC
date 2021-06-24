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

class XeroController extends Controller
{
    private function getOAuth2()
    {
        // This will use the 'default' app configuration found in your 'config/xero-laravel-lf.php` file.
        // If you wish to use an alternative app configuration you can specify its key (e.g. `new OAuth2('other_app')`).
        return new OAuth2();
    }

    public function redirectUserToXero()
    {
        // Step 1 - Redirect the user to the Xero authorization URL.
        return $this->getOAuth2()->getAuthorizationRedirect();
    }

    public function handleCallbackFromXero(Request $request)
    {
        // Step 2 - Capture the response from Xero, and obtain an access token.
        $accessToken = $this->getOAuth2()->getAccessTokenFromXeroRequest($request);
        
        // Step 3 - Retrieve the list of tenants (typically Xero organisations), and let the user select one.
        $tenants = $this->getOAuth2()->getTenants($accessToken);
        $selectedTenant = $tenants[0]; // For example purposes, we're pretending the user selected the first tenant.

        // dd($accessToken);
        // dd(json_encode($accessToken));
        // dd($selectedTenant->tenantId);

        // Step 4 - Store the access token and selected tenant ID against the user's account for future use.
        // You can store these anyway you wish. For this example, we're storing them in the database using Eloquent.
        // $user = auth()->user();
        // $user->xero_access_token = json_encode($accessToken);
        // $user->tenant_id = $selectedTenant->tenantId;
        // $user->save();

        $config = Configuration::getDefaultConfiguration()->setAccessToken( $accessToken );       

        $apiInstance = new AccountingApi(
            new Client(),
            $config
        );
        $xeroTenantId = $selectedTenant->tenantId;
        $invoiceID = "c52f2b26-d6a9-423e-b929-ffade18c656d";

        try {
        $result = $apiInstance->getInvoiceAsPdf($xeroTenantId, $invoiceID);
        print_r($result);
        exit();
        
        } catch (Exception $e) {
        echo 'Exception when calling AccountingApi->getInvoiceAsPdf: ', $e->getMessage(), PHP_EOL;
        }

    }

    public function refreshAccessTokenIfNecessary()
    {
        // Step 5 - Before using the access token, check if it has expired and refresh it if necessary.
        $user = auth()->user();
        $accessToken = new AccessToken(json_decode($user->xero_access_token));

        if ($accessToken->hasExpired()) {
            $accessToken = $this->getOAuth2()->refreshAccessToken($accessToken);

            $user->xero_access_token = $accessToken;
            $user->save();
        }
    }
}
