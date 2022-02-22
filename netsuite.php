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
$fields = isset($_POST['fields']) ? $_POST['fields'] : array();

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

	$service->setPreferences(false, true, false, true);

	$updateResponse = $service->update($request);
	if (!$updateResponse->writeResponse->status->isSuccess) {
	    echo "Update error. Please conctact support@ctg.us" . json_encode($updateResponse->writeResponse->status);
	} else {
	   //Sending Email
		$mail = new PHPMailer(true);
		$mail->setFrom('admin@ctg.us', 'CTG admin');
		$mail->addAddress('support@ctg.us', 'Aleksandar Djokic');
		$mail->addCC('isis@ctg.us','Isis Dorado');
		$mail->addCC('orders@ctg.us','CTG Orders');
		// $mail->addCC('shaun@ctg.us','Kristin Arrowsmith');
		$mail->isHTML(true);
		if($fields) {
			$mail->Subject  = 'ACTION REQUIRED: ' . $poRecord->entity->name . ' confirmed ' .  $poRecord->tranId;
		} else {
			$mail->Subject  = $poRecord->entity->name . ' confirmed ' .  $poRecord->tranId;
		}
		$mail->Body = generateMessage('PO sent ' . substr($poRecord->createdDate, 0, strpos($poRecord->createdDate, 'T')) . '</br>Email sent to:  ' . $user . '</br>Confirmed: ' . date("Y-m-d") .  '</br>Days to confirm: ' . $daysDiff, $fields);
		if(!$mail->send()) {
		  echo 'Message was not sent.';
		  return 'Message was not sent.';
		} else {
		  // echo 'Message has been sent.';
		}
		echo json_encode('Email sent!');
		return json_encode('Email sent!');
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

function generateMessage($info, $fields) {
	
	$logo = '<img src="https://644154.app.netsuite.com/core/media/media.nl?id=1328&c=644154&h=110c8a2ef5af5028fab7&fcts=20100514125722&whence=" />';
	$style = '<style type="text/css">html *
	{
	font-size: 10.5pt!important;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
	font-weight: 300;
	}
	#firstName {
	font-size: 10.5pt !important;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
	font-weight: 300;
	}
	p {
	font-size: 10.5pt !important;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
	font-weight: 300;
	}  
	span {
	font-size: 10.5pt !important;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
	font-weight: 300;
	}
	td {
		font-size: 10.5pt !important;
	font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
	font-weight: 300;
	}
	
	#loginButton {
			font-family: "Calibri";
			font-size: 18px;
			font-weight: normal;
			text-align: center;
			text-decoration: none;
			padding-top: 15px;
			padding-right: 20px;
			padding-bottom: 15px;
			padding-left: 20px;
			background-color: #8CB36B;
			color:white;
			float:right;
		} 
		.temp-container {
			border-bottom: 10px solid #a8c791;
			border-top: 10px solid #a8c791;
		}
		body {
			font-family: "Calibri";
			font-size: 12pt;
		}
	</style>';


	$bodyHtml = '';
	$bodyHtml.=$style;
	$bodyHtml.=$logo;
	if($fields) {
		$confirmationHtml ='<h2>Manufacturer requested changes:</h2>';
		$confirmationHtml.= '
		<table style="border: 1px solid black; width:50%">
			<thead>
				<th style="border: 1px solid black;">Field</th>
				<th style="border: 1px solid black;">Old Value</th>
				<th style="border: 1px solid black;">New Value</th>
			</thead>
			<tbody>';
		foreach($fields as $field) {
			$confirmationHtml.= '<tr style="border: 1px solid black;">
			<td style="border: 1px solid black;">' . $field['field'] . '</td>
			<td style="border: 1px solid black;">' . $field['oldValue'] . '</td>
			<td style="border: 1px solid black;">' . $field['newValue'] . '</td>
		</tr>';
		}
		
		$confirmationHtml.='</tbody></table>';
		
		$bodyHtml.=$confirmationHtml;
		$bodyHtml.=$info;
	}
	else {
		$bodyHtml ='<h2>Manufacturer confirmed! No changes requested!</h2>';
		$bodyHtml.=$info;
	}

	return $bodyHtml;
}

?>
</body>


</html>

