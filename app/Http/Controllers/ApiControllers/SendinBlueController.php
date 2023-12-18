<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SendinBlue;
use GuzzleHttp\Client;
use App\Models\FacebookPage;
class SendinBlueController extends Controller
{    



    public function getList($page_id){
        $page = FacebookPage::find($page_id);
        // Configure API key authorization: api-key
        $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $page->sendinblue_key);
        // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
        // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('api-key', 'Bearer');
        // Configure API key authorization: partner-key
        // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
        // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('partner-key', 'Bearer');

        $apiInstance = new SendinBlue\Client\Api\ContactsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        $limit = 10; // int | Number of documents per page
        $offset = 0; // int | Index of the first document of the page
        $sort = "desc"; // string | Sort the results in the ascending/descending order of record creation. Default order is **descending** if `sort` is not passed

        try {
            $result = $apiInstance->getLists($limit, $offset, $sort);
       
            return $result;
        } catch (Exception $e) {
            echo 'Exception when calling ContactsApi->getLists: ', $e->getMessage(), PHP_EOL;
        }
    }
    public function AddUser($email_address,$form,$name){
        $page = FacebookPage::find($form->page_id);

        try{try{
                $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $page->sendinblue_key);
                $apiInstance = new SendinBlue\Client\Api\ContactsApi(
                    new Client(),
                    $config
                );
               
                $createContact = new \SendinBlue\Client\Model\CreateContact(); // Values to create a contact
                $createContact['email'] = $email_address;
                $createContact['attributes'] = ['FNAME'=>$name];

                $createContact['listIds'] = [$form->id_page_sendinblue];

                try {
                    $result = $apiInstance->createContact($createContact);
                    print_r($result);
                } catch (\Exception $e) {
                        print_r($e);
                    echo 'Exception when calling ContactsApi->createContact: ', $e->getMessage(), PHP_EOL;
                }
            }catch(\Exception $e){

            }}catch(\Throwable $t){

            }       
         
      
    }
  
}


