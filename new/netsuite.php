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

	$service->setPreferences(false, false, false, true);

	$updateResponse = $service->update($request);
	if (!$updateResponse->writeResponse->status->isSuccess) {
	    echo "Update error. Please conctact support@ctg.us";
	} else {
	   //Sending Email
		$mail = new PHPMailer(true);
		$mail->setFrom('admin@ctg.us', 'CTG admin');
		$mail->addAddress('support@ctg.us', 'Aleksandar Djokic');
		// $mail->addCC('christopher@ctg.us','Christopher Cornish');
		// $mail->addCC('kristin@ctg.us','Kristin Arrowsmith');
		// $mail->addCC('shaun@ctg.us','Kristin Arrowsmith');
		$mail->isHTML(true);
		$mail->Subject  = $poRecord->entity->name . ' confirmed ' .  $poRecord->tranId;
		$mail->Body = generateMessage('PO sent ' . substr($poRecord->createdDate, 0, strpos($poRecord->createdDate, 'T')) . '</br>Email sent to:  ' . $user . '</br>Confirmed: ' . date("Y-m-d") .  '</br>Days to confirm: ' . $daysDiff, $fields);
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

	$signature ='<p><span><span style="color: black;">Thank you very much,</span></span></p>

	<div style="background-color:white">
	<div class="x_WordSection1" style="line-height: 0;">
	<p style="margin-bottom: 0px;"><span><b><span style="color:#306700">CTG US, LLC</span></b></span></p>

	<p style="margin-bottom: 0px;"><span><span style="color: black;">We think globally.</span></span></p>

	<p style="margin-bottom: 0px;"><span><span style="color: rgb(48, 103, 0);">-------------------------</span><span style="color: rgb(48, 103, 0);">&nbsp;&nbsp;</span></span></p>

	<p style="margin-bottom: 0px;"><span><b><span style="font-family: &quot;Trebuchet MS&quot;; color: rgb(62, 104, 49);">(T)&nbsp;</span></b><span style="color: rgb(62, 104, 49);">+1 305 394 9446</span></span></p>

	<p style="font-size: 14px; margin-bottom: 0px;"><span style="color: black;">&nbsp;</span></p>

	<p style="font-size: 14px; margin-bottom: 0px;"><span style="color: rgb(62, 104, 49);">5150 NW 109th Ave, Suite 4</span></p>

	<p style="font-size: 14px; margin-bottom: 0px;"><span style="color: rgb(62, 104, 49);">Sunrise, FL 33351</span></p>

	<p style="font-size: 14px; margin-bottom: 0px;"><span style="color: rgb(62, 104, 49);">USA</span></p>

	<p style="font-size: 14px; margin-bottom: 0px;"><span style="color: rgb(62, 104, 49);">&nbsp;</span></p>

	<p style="margin-bottom: 0px;"><span style="color:#3E6831">&nbsp;</span></p>

	<p style="margin-bottom: 0px;"><span>&nbsp;</span></p>
	</div>
	</div>';
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

	$bodyHtml.=$signature;

	return $bodyHtml;
}

?>
</body>


</html>

