<?php
use Dompdf\Dompdf;

function downloadVoucherPdf($vid)
{
    QRcode::png('PHP QR Code :)');
    
    // instantiate and use the dompdf class
//    $dompdf = new Dompdf();
//    $dompdf->loadHtml('hello world');
//
//    // (Optional) Setup the paper size and orientation
//    $dompdf->setPaper('A4', 'landscape');
//
//    // Render the HTML as PDF
//    $dompdf->render();
//
//    // Output the generated PDF to Browser
//    $dompdf->stream();
}
?>