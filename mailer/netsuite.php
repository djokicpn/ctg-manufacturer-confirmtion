	<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once 'mailer/PHPMailer.php';
require_once 'mailer/SMTP.php';
require_once 'mailer/Exception.php';

require_once 'PHPToolkit/NetSuiteService.php';

$poId = $_GET['id'];
$token = $_GET['token'];
$user = $_GET['user'];


$manufacturer = new Manufacturer();

$date = 'Confirmed on ' . date('d/m/Y') . ' by ' . strtolower($user);

$service = new NetSuiteService();
$gr = new GetRequest();
$gr->baseRef = new RecordRef();
$gr->baseRef->internalId = $manufacturer->getPoId();
$gr->baseRef->type = "purchaseOrder";

$getResponse = $service->get($gr);

$poRecord = $getResponse->readResponse->record;

$customFieldListListArray = $poRecord->customFieldList->customField;

$po2 = $getResponse->readResponse->record;
$po2->itemList->replaceAll = false;

$now = time();
$dateCreated = strtotime(substr($poRecord->createdDate, 0, strpos($poRecord->createdDate, 'T')));
$datediff = $now - $dateCreated;

$daysDiff = round($datediff / (60 * 60 * 24));

 if (is_array($customFieldListListArray)) 
    {
        foreach ($customFieldListListArray as $field) 
        {	
        	$fieldId = $field->scriptId;
	        if ($fieldId == "custbody_manufacturerconfirmation") {
	            $fieldValue = $field->value;
	            $confirmed = $fieldValue;
	        } else if ($fieldId == "custbody_manufacturer_conf_date") {
	            $fieldValue = $field->value;
	            $confirmedBy = $fieldValue;
	        } 

        }     
    } 
//Setting checkbox value
$manufacturerConfirmationField = new BooleanCustomFieldRef();
$manufacturerConfirmationField->scriptId = 'custbody_manufacturerconfirmation';
$manufacturerConfirmationField->value = true;

if(intval($confirmed)!==1) {
	$timestamp = new StringCustomFieldRef();
	$timestamp->scriptId = 'custbody_manufacturer_conf_date';
	$timestamp->value = $date;

	$po2->customFieldList->customField = array($manufacturerConfirmationField,$timestamp);

	$request = new UpdateRequest();
	$request->record = $po2;

	$service->setPreferences(false, false, false, true);

	$updateResponse = $service->update($request);
	if (!$updateResponse->writeResponse->status->isSuccess) {
	    echo "UPDATE ERROR";
	} else {
	   //Sending Email
		$mail = new PHPMailer(true);
		$mail->setFrom('admin@michaelcornish.com', 'CTG admin');
		$mail->addAddress('support@ctg.us', 'Aleksandar Djokic');
		$mail->addCC('christopher@ctg.us','Christopher Cornish');
		$mail->addCC('kristin@ctg.us','Kristin Arrowsmith');
		$mail->isHTML(true);
		$mail->Subject  = $poRecord->entity->name . ' confirmed ' .  $poRecord->tranId;
		$mail->Body = 'PO sent ' . substr($poRecord->createdDate, 0, strpos($poRecord->createdDate, 'T')) . '</br>Email sent to:  ' . $user . '</br>Confirmed: ' . date("Y-m-d") .  '</br>Days to confirm: ' . $daysDiff;
		if(!$mail->send()) {
		  echo 'Message was not sent.';
		  echo 'Mailer error: ' . $mail->ErrorInfo;
		} else {
		  // echo 'Message has been sent.';
		}
		include_once 'confirmed.php';
	}
}
else
	include_once 'already_confirmed.php';
return true;

class Manufacturer {
	public function getPoId() {
		return $_GET['id'];
	}
	public function getToken() {
		return 'JkajdU8Z11pxMksiudAUdh';
	}
}

?>
</body>


</html>

