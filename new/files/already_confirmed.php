<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>CTG Manufacturer Confirmation</title>
</head>
<body style = 'text-align: center; margin-top:15%'>
	<img style = 'padding-left:130px' src="ctg_logo.png">


	<p style = 'font-weight: bold;'>Look’s like you’re really on the ball. <?php echo $poRecord->tranId ?> </br> has already been confirmed on <?php echo substr($confirmedBy, strpos($confirmedBy, 'at') + 3); ?>.</p>
	<p>If you would like to notify us of any changes please reach out to one</br> of the following departments to address:</p>
 
<p>Planning: kristin@ctg.us</br>
Logistics: isis@ctg.us</br>
Finance: quan@ctg.us</p> 
 
<p>Thank you.</p>
</body>
</html>
