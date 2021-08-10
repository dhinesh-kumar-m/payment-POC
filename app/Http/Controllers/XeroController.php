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
use Illuminate\Support\Facades\Log;

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
        // $json=json_encode($accessToken);
        // dd(json_decode($json, true));
        // dd($selectedTenant->tenantId);

        // Step 4 - Store the access token and selected tenant ID against the user's account for future use.
        // You can store these anyway you wish. For this example, we're storing them in the database using Eloquent.
        $user = UserMaster::find(1);
        $user->xero_access_token = json_encode($accessToken);
        $user->tenant_id = $selectedTenant->tenantId;
        $user->save();
        $successMessage="Xero is Connected Successfully";
        return $this->successResponse($user, $successMessage, 200);

    }

    public function refreshAccessTokenIfNecessary()
    {
        // Step 5 - Before using the access token, check if it has expired and refresh it if necessary.
        $user = auth()->user();
        // dd(json_decode($user->xero_access_token,true));
        $accessToken = new AccessToken(json_decode($user->xero_access_token,true));

        if ($accessToken->hasExpired()) {
            $accessToken = $this->getOAuth2()->refreshAccessToken($accessToken);

            $user->xero_access_token = json_encode($accessToken);
            dd(json_encode($accessToken));
            $user->save();
            $successMessage="New Access Token generated";
            return $this->successResponse($user, $successMessage, 200);
        }
        $successMessage="Token Didn't expired";
        return $this->successResponse($user, $successMessage, 200);
    }
    public function testapi()
    {
        $user = auth()->user();
        // $user->first_name="dhinesh";
        // $user->last_name="kumar";
        // $user->save();
        print_r($user);
        exit();
    }

    public function create_contact(Request $request)
    {
        $user = auth()->user();
        $accessToken= $user->xero_access_token;
        $tenantId= $user->tenant_id;

        $config = Configuration::getDefaultConfiguration()->setAccessToken( $accessToken );       

        $apiInstance = new AccountingApi(
            new Client(),
            $config
        );
        $xeroTenantId = $tenantId;
        $summarizeErrors = false;

        $phone = new Phone;
        $phone->setPhoneNumber('3214567890');
        $phone->setPhoneType(Phone::PHONE_TYPE_MOBILE);
        $phones = [];
        array_push($phones, $phone);

        $contact = new Contact;
        $contact->setName('Balaji');
        $contact->setEmailAddress('Balaji@avengers.com');
        $contact->setPhones($phones);

        $contacts = new Contacts;
        $arr_contacts = [];
        array_push($arr_contacts, $contact);
        $contacts->setContacts($arr_contacts);

        try {
        $result = $apiInstance->createContacts($xeroTenantId, $contacts, $summarizeErrors);
        return $this->successResponse($result, "Contact created Successfully", 200);
        } catch (Exception $e) {
            $errorMessage = 'Something Wrong';
			return $this->errorResponse($errorMessage, 400);
        }
    }

    public function create_invoice(Request $request){
        $user = auth()->user();
        $accessToken= $user->xero_access_token;
        $tenantId= $user->tenant_id;

        $config = Configuration::getDefaultConfiguration()->setAccessToken( $accessToken );       

        $apiInstance = new AccountingApi(
            new Client(),
            $config
        );
        $xeroTenantId = $tenantId;
        $summarizeErrors = false;
        $unitdp = 4;
        $dateValue = new DateTime('2021-06-24');
        $dueDateValue = new DateTime('2021-06-28');

        $contact = new Contact;
        $contact->setContactID('fd6abc69-f296-4c36-af90-6af4507e9a8d');

        $lineItemTracking = new LineItemTracking;
        $lineItemTracking->setTrackingCategoryID('00000000-0000-0000-0000-000000000000');
        $lineItemTracking->setTrackingOptionID('00000000-0000-0000-0000-000000000000');
        $lineItemTrackings = [];
        array_push($lineItemTrackings, $lineItemTracking);
        

        $lineItem = new LineItem;
        $lineItem->setDescription('Foobar');
        $lineItem->setQuantity(1.0);
        $lineItem->setUnitAmount(12345.0);
        $lineItem->setAccountCode('000');
        $lineItem->setTracking($lineItemTrackings);
        $lineItems = [];
        array_push($lineItems, $lineItem);

        $invoice = new Invoice;
        $invoice->setType(Invoice::TYPE_ACCREC);
        $invoice->setContact($contact);
        $invoice->setDate($dateValue);
        $invoice->setDueDate($dueDateValue);
        $invoice->setLineItems($lineItems);
        $invoice->setReference('Website Design');
        $invoice->setStatus(Invoice::STATUS_DRAFT);

        $invoices = new Invoices;
        $arr_invoices = [];
        array_push($arr_invoices, $invoice);
        $invoices->setInvoices($arr_invoices);

        try {
        $result = $apiInstance->createInvoices($xeroTenantId, $invoices, $summarizeErrors, $unitdp);
        return $this->successResponse($result, "Invoice created Successfully", 200);
        } catch (Exception $e) {
        echo 'Exception when calling AccountingApi->createInvoices: ', $e->getMessage(), PHP_EOL;
        }
    }

}
