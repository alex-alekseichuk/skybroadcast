<?php
// //Events @1-F81417CB

//header_BeforeShow @1-9D7A1A79
function header_BeforeShow()
{
    $header_BeforeShow = true;
//End header_BeforeShow

//Custom Code @2-781B43E6
// -------------------------
    global $header;
	If ($FileName == "index.php")
	{
		print "<b>INDEX</b>";
	}

    // Write your own code here.
// -------------------------
//End Custom Code

//Close header_BeforeShow @1-C6915AF4
    return $header_BeforeShow;
}
//End Close header_BeforeShow


?>
