<?php 
    if(isset($_POST['fields'])) {
        $fields = $_POST['fields'];
        echo json_encode($fields);
    } else 
    echo 'No changes has been made';
?>

<div style = 'text-align: center;'>
	<img style = 'padding-left:130px' src="ctg_logo.png">
<p style = 'font-weight: bold;'>Thank you for confirming our purchase order.</p>
<p>Now go make some great product!</p>
</div>