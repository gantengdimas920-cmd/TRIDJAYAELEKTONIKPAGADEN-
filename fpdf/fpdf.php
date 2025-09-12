<?php
// Dummy FPDF untuk placeholder
class FPDF {
    function AddPage() {}
    function SetFont($family, $style, $size) {}
    function Cell($w, $h, $txt, $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {}
    function Ln($h = null) {}
    function Output($dest = '', $name = '', $isUTF8 = false) {
        echo "PDF dummy generated!";
    }
}
?>
