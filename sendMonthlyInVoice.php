<?php
/**
* Date: 29th Dec 2016
  Author: Arun Billur
*/
//require_once(realpath(__DIR__. DIRECTORY_SEPARATOR . '../..')."/utility/S3.php");
require('WriteHTML.php');
//require_once('dbConnection.php');
$db = new dbConnection();
$count = 0;
$taxRate = 0.15;
$date = date('d/m/Y',strtotime('+5 hour +30 minutes'));
$dueDate = date('d/m/Y',strtotime('+ 5 day +5 hour +30 minutes'));
$startDate = date('Y-m-d',strtotime('- 1 month +5 hour +30 minutes'));
$toDate = date('Y-m-d',strtotime('- 1 day +5 hour +30 minutes'));
$month = date('F',strtotime('+5 hour +30 minutes'));
$lsMailIds = array('arun.billur@letsservice.in ','sanchitagrawal@letsservice.in');

$getServiceCenterList = mysql_query("SELECT serviceCenterBranch,serviceCenterDescription,AMCPrice,serviceCenterEmail,count(*) FROM lsSCPrivilegeCardPrice where AMCPrice is not null and AMCStatus = 'true' group by 1,2,3,4");
	while ($getServiceCenterListRlt = mysql_fetch_assoc($getServiceCenterList)){
		$eachAmcPrice = $getServiceCenterListRlt['AMCPrice'];
		$branchids = $getServiceCenterListRlt['serviceCenterBranch'];
		$scName = $getServiceCenterListRlt['serviceCenterDescription'];
		$scMails = explode(",", $getServiceCenterListRlt['serviceCenterEmail']);
		$mailDetails = array_merge($scMails,$lsMailIds);
			$value['amcPrice'];
			$detailsQuery = mysql_query("SELECT lpcs.PCUserId,lu.username,lu.user_mobile,(select lpc.cardNo from lsPrivilegeCard as lpc where lpc.lsPCId = lpcs.PCID)as cardNo,lpcs.bikeNo,lpcs.chassisNo,(select u.username from lsUsers as u where u.userId = lpcs.serviceCenterId and user_role = 'OPD')as scName,DATE(lpcs.validFrom) as validFrom,DATE(lpcs.validTo) validTo,lpcs.remarks,lpcs.status,lpcs.bookletNo,lpcs.create_ts FROM lsUsers as lu,lsPrivilegeCardUsers as lpcs where lpcs.PCUserId = lu.userId and lpcs.serviceCenterId in(".$branchids.") and DATE(lpcs.validFrom) between "."'".$startDate."'"." and "."'".$toDate."'"." and lpcs.status = 'active' order by lpcs.create_ts desc");
			$getAmcCount = mysql_query("SELECT count(lsPCUId) as amcCount from lsPrivilegeCardUsers where status ='active' and serviceCenterId in(".$branchids.") and DATE(validFrom) between "."'".$startDate."'"." and "."'".$toDate."'"."");
			if(mysql_num_rows($getAmcCount)==0){
					$pickDropCount = 0;
					$totalAmcAmount = 0;
					$serviceTax = 0;
					$swachhBharat = 0;
					$krishiKalyan = 0;
					$taxAmount = 0;
					$totalAmountInctax = 0; 
			}
			else{
			while ($getAmcCountRlt = mysql_fetch_assoc($getAmcCount)){
					$pickDropCount = $getAmcCountRlt['amcCount'];
					$totalAmcAmount = $getAmcCountRlt['amcCount'] * $eachAmcPrice;
					$serviceTax = $totalAmcAmount * 0.14;
					$swachhBharat = $totalAmcAmount * 0.005;
					$krishiKalyan = $totalAmcAmount * 0.005;
					$taxAmount = $totalAmcAmount * $taxRate;
					$totalAmountInctax = $totalAmcAmount + $taxAmount; 
				}
			}
$pdf=new PDF();
$pdf->AddPage();
$pdf->Rect(5, 5, 200, 271, 'D');
$pdf->SetFont('Arial','B',12);

$html='<br><br><table border="0">
<tr>
<td width="200" height="40">
LetsService Automotive Technologies Pvt Ltd.<br>
# 153,Sector 5, Next to Devi Eye<br>
						Hospital,HSR Layout<br>
						Bangalore, Karnataka 560102<br>
						IN<br>
						9535676767<br>
						accounts@letsservice.in<br>
						ST No: AACCL9409PSD001<br>
						SBC No: AACCL9409PSD001<br>
						KKC No: AACCL9409PSD001<br></td>
						<td width="200" height="30">'.$pdf->Image("https://s3-us-west-2.amazonaws.com/letsservicedealer/letsservice.jpeg",140,10,63).'</td>
</tr>
</table><br>';
$pdf->WriteHTML($html);
$pdf->SetFont( 'Arial', 'B', 10 ); 
$invoiceTo = 'INVOICE TO';
$pdf->Cell(0, 10, $invoiceTo, 0, 2, 'L');
$pdf->SetFont('Arial','B',10);
$html='<table border="0">
<tr>
<td width="480" height="60">'.$scName.' Pvt Ltd. </td><td width="280" height="60" bgcolor="#80e5ff">Tax Invoice LS1898</td>
</tr>
<tr>
<td width="480" height="60">('.$startDate.' to '.$toDate.')</td><td width="280" height="60" bgcolor="#80e5ff">DATE '.$date.' TERMS Due on receipt</td>
</tr>
<tr>
<td width="480" height="60">Service Tax No: AAXFM2014RSD002</td><td width="280" height="60" bgcolor="#80e5ff">DUE DATE '.$dueDate.'</td>
</tr>
</table><br>';
$pdf->WriteHTML($html);
$pdf->SetFont('Arial','B',10);
//$pdf->SetFont('Arial','',10);
$html='<br><br><table border="1">
<tr>
<td width="100" height="60" bgcolor="#80e5ff">NO</td>
<td width="420" height="60" bgcolor="#80e5ff">ACTIVITY</td><td width="80" height="60" bgcolor="#80e5ff"> QTY </td><td width="80" height="60" bgcolor="#80e5ff"> RATE </td><td width="80" height="60" bgcolor="#80e5ff"> AMOUNT </td>
</tr>
<tr>
<td width="100" height="60">1</td>
<td width="420" height="60"> '.$month.' LS Prepaid AMC Inclusion </td><td width="80" height="60"> '.$pickDropCount.' </td><td width="80" height="60"> '.$eachAmcPrice.' </td><td width="80" height="60"> '.$totalAmcAmount.' </td>
</tr>
</table><br>';
$pdf->WriteHTML($html);
//$pdf->SetFont('Arial','',10);
    $pdf->SetFont( 'Arial', 'B', 10 ); 
$totalAmcAmountColumn = '
    SUB TOTAL 	              '.$totalAmcAmount.' ';
$pdf->Cell(0, 10, $totalAmcAmountColumn, 0, 2, 'R');
$taxAmountColumn = '
    TAX  	               '.$taxAmount.' ';
$pdf->Cell(0, 10, $taxAmountColumn, 0, 3, 'R');
$totalAmountInctaxColumn = '
    TOTAL 	              '.$totalAmountInctax.' ';
$pdf->Cell(0, 10, $totalAmountInctaxColumn, 0, 3, 'R');
$totalAmountInctaxDueColumn = '
    DUE TOTAL 	              '.$totalAmountInctax.' ';
$pdf->Cell(0, 10, $totalAmountInctaxDueColumn, 0, 3, 'R');
$taxSummary = '
    TAX SUMMARY';
$pdf->Cell(0, 10, $taxSummary, 0, 3, 'L');

$pdf->SetFont('Arial','B',10);
$html='<table border="">
<tr>
<td width="460" height="60" bgcolor="#80e5ff"> RATE</td><td width="150" height="60" bgcolor="#80e5ff"> Tax </td>
<td width="150" height="60" bgcolor="#80e5ff"> NET </td>
</tr>
<tr>
<td width="460" height="60"> Service Tax @ 14%</td><td width="150" height="60"> '.$serviceTax.' </td>
<td width="150" height="60"> '.$totalAmcAmount.'</td>
</tr>
<tr>
<td width="460" height="60"> Swachh Bharat Cess @ 0.5% </td><td width="150" height="60"> '.$swachhBharat.'</td>
<td width="150" height="60"> '.$totalAmcAmount.'</td>
</tr>
<tr>
<td width="460" height="60"> Krishi Kalyan Cess @ 0.5% </td><td width="150" height="60"> '.$krishiKalyan.' </td>
<td width="150" height="60"> '.$totalAmcAmount.'</td>
</tr>
</table><br><br>';
$pdf->WriteHTML($html);
$pdf->SetY( -26 );
$pdf->SetFont( 'Arial', 'B', 10 ); 
$footer = '2015 LetsService Automotive Technologies Pvt. Ltd. All Rights Reserved';
$pdf->Cell(0, 5, $footer, 1, 1, 'C');

$pdf->SetFont('Arial','B',10);
$html = '<table border="1"><tr>
<td width="100" height="40" bgcolor="#80e5ff"> Sl No</td><td width="200" height="40" bgcolor="#80e5ff"> Custome Name </td><td width="150" height="40" bgcolor="#80e5ff"> Custome Mobile </td><td width="100" height="40" bgcolor="#80e5ff"> cardNo</td><td width="100" height="40" bgcolor="#80e5ff"> Bike No</td><td width="130" height="40" bgcolor="#80e5ff"> Sold Date</td>
</tr></table>';
$pdf->WriteHTML($html);
if(mysql_num_rows($detailsQuery)!=0){
	$pdf->SetFont('Arial','B',10);
	while($detailsRlt = mysql_fetch_assoc($detailsQuery)){
					$username = $detailsRlt['username'];
	        		$count = $count + 1;
	        		$user_mobile = $detailsRlt['user_mobile'];
		            $cardNo = $detailsRlt['cardNo'];
		            $bikeNo = $detailsRlt['bikeNo'];
		            $chassisNo = $detailsRlt['chassisNo'];
		            $scName = $detailsRlt['scName'];
		            $validFrom = $detailsRlt['validFrom'];
		            $validTo = $detailsRlt['validTo'];


	$html='<table border="1">
	<tr>
	<td width="100" height="40"> '.$count.'</td><td width="200" height="40"> '.$username.' </td><td width="150" height="40" > '.$user_mobile.' </td><td width="100" height="40"> '.$cardNo.'</td><td width="100" height="40"> '.$bikeNo.'</td><td width="130" height="40"> '.$validFrom.'</td>
	</tr>
	</table>';

	$pdf->WriteHTML($html);
}
}
//$fileatt = $pdf->Output('abc.pdf','S');
$to = 'arun.billur@letsservice.in';
$subject = $scName.'_'.$month.'_Prepaid Invoice';
$repEmail = 'arun.billur@letsservice.in';

$fileName = $scName.'_inVoice_'.$month.'.pdf';
$fileatt = $pdf->Output($fileName, 'S');
$attachment = chunk_split(base64_encode($fileatt));
$eol = PHP_EOL;
$separator = md5(time());

$headers = 'From: '.$month.' InVoice'.$eol;
$headers .= 'MIME-Version: 1.0' .$eol;
$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

$message = "--".$separator.$eol;
$message .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
$message .= "Hello, 
			 Please find attachemnt for the December month Invoice." .$eol;

$message .= "--".$separator.$eol;
$message .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
$message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;

$message .= "--".$separator.$eol;
$message .= "Content-Type: application/octet-stream; name=\"".$fileName."\"".$eol; 
$message .= "Content-Transfer-Encoding: base64".$eol;
$message .= "Content-Disposition: attachment".$eol.$eol;
$message .= $attachment.$eol;
$message .= "--".$separator."--";

foreach ($mailDetails as $mailDetail) {
	if (mail($mailDetail, $subject, $message, $headers)){
		echo "Email sent";
	}
	else {
		echo "Email failed";
	}
}
}
?>