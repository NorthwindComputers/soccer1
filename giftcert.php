<?php
/******************************
 * Gift Certificate retrieval
 *
 * programmed by: Richard Tuttle
 * updated: 10 December 2014
 *******************************/
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache'); 
require_once 'cpadmin/includes/db.php';
require_once('tcpdf/config/tcpdf_config.php');
require_once 'tcpdf/tcpdf.php';

$h = $_GET["h"];
$sql = "SELECT DISTINCT * FROM certificate WHERE hash='$h' AND certType='gift'";
$result = mysql_query($sql) or die("Gift Certificate Retreival Error: " . mysql_error());
$gcInfo = mysql_fetch_array($result);
if ($result) {
    $gcNum = $gcInfo['codeNum'];
    $gcValue = $gcInfo['origValue'];
    
    class MYPDF extends TCPDF {
        public function Header() {
            $bMargin = $this->getBreakMargin();
            $auto_page_break - $this->AutoPageBreak;
            $this->SetAutoPageBreak(false, 0);
            $this->Image('gift-card-online1.jpg', 15, '', 600, '', 'JPG', 'https://soccerone.com', 'M', FALSE, 150, 'C', false, false, 1, false, false, TRUE);
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            $this->setPageMark();
        }
    }
    
	// create pdf document with gc data
	// create new PDF document
	// $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); // PDF_PAGE_ORIENTATION
    $pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Richard Tuttle');
    $pdf->SetTitle('Soccer One Gift Certificate');

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins('0', '5', '6', true); // PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    $pdf->setPrintFooter(false);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
    }

    // set font
    $pdf->SetFont('dejavusans', '', 18);

    $pdf->SetRightMargin('21');

    // Add a page
    $pdf->AddPage();
    
    // html content
    $html = '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>'.$gcNum;
    // $html = '<div style="margin-right:40px;margin-top:100px;">'.$gcNum.'</div>';
    $pdf->writeHTML($html, true, false, true, false, 'R');
 
    // Close and output PDF document
    $pdf->Output('giftcertificate.pdf', 'I');
} else {
	echo "INVALID CERTIFICATE REFERENCE!"; 
	exit; 
}
?>