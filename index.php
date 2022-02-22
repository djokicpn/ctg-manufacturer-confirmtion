<html>
<head>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="./css/bootstrap-datepicker.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="./js/bootstrap-datepicker.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    <style type="text/css">
	.back-link a {
		color: #4ca340;
		text-decoration: none; 
		border-bottom: 1px #4ca340 solid;
	}
	.back-link a:hover,
	.back-link a:focus {
		color: #408536; 
		text-decoration: none;
		border-bottom: 1px #408536 solid;
	}
	.entry-header {
		text-align: left;
		margin: 0 auto 50px auto;
		width: 80%;
        max-width: 978px;
		position: relative;
		z-index: 10001;
	}
	#demo-content {
		padding-top: 100px;
		text-align: center;
	}
	#content {
		text-align: center;
	}
	p {
		color: black;
	}
	</style>
  </head>
  <!-- <div class="demo">		
    <div id="demo-content">
      <div id="loader-wrapper">
        <div id="loader"></div>
        <div class="loader-section section-left"></div>
              <div class="loader-section section-right"></div>
      </div>
    </div>
  </div> -->
  <?
  
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
$custbodylist_shipment_type = '';
$custbody_number_of_pallets = '';
$custbody_stickeredfor = '';
$custbody_pickuplocation = '';
$custbody_pick_up_system = '';
$custbody_pomessagefromproforma = '';

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
	            $confirmed = $field->value;
	        } else if ($fieldId == "custbody_manufacturer_conf_date") {
              $confirmedBy = $field->value;
          } else if ($fieldId == "custbodylist_shipment_type") {
              $custbodylist_shipment_type = $field->value;
          } else if ($fieldId == "custbody_number_of_pallets") {
              $custbody_number_of_pallets = $field->value;
          } else if ($fieldId == "custbody_stickeredfor") {
              $custbody_stickeredfor = $field->value;
          } else if ($fieldId == "custbody_pickuplocation") {
              $custbody_pickuplocation = $field->value;
          } else if ($fieldId == "custbody_pick_up_system") {
              $custbody_pick_up_system = $field->value;
          } else if ($fieldId == "custbody_pomessagefromproforma") {
              $custbody_pomessagefromproforma = $field->value;
          }  

        }     
    } 
    if(intval($confirmed)==1) {
      include_once('already_confirmed.php');
    }
    else {
      include_once('not_confirmed.php');
    }
class Manufacturer {
  public function getPoId() {
    return $_GET['id'];
  }
  public function getToken() {
    return 'JkajdU8Z11pxMksiudAUdh';
  }
}
  ?>
  <script type="text/javascript">
  $('.demo').addClass('loaded');
  </script>

  
  
  