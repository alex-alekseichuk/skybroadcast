<?php
//Include Common Files @1-83C617B7
define("RelativePath", ".");
define("PathToCurrentPage", "/");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-9F10089B
// Variables
$FileName = "";
$Redirect = "";
$Tpl = "";
$TemplateFileName = "";
$BlockToParse = "";
$ComponentName = "";

// Events;
$CCSEvents = "";
$CCSEventResult = "";

$FileName = "temp_header.php";
$Redirect = "";
$TemplateFileName = "temp_header.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-EF90B696

// Controls
$header = new clsheader("");
$header->BindEvents();
$header->Initialize();
$footer = new clsfooter("");
$footer->BindEvents();
$footer->Initialize();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");

$Charset = $Charset ? $Charset : $TemplateEncoding;
if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-04FA3713
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate(PathToCurrentPage . $TemplateFileName, "main", $TemplateEncoding);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-2D944FA9
$header->Operations();
$footer->Operations();
//End Execute Components

//Go to destination page @1-F75C3D40
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-5655AB10
$header->Show("header");
$footer->Show("footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-007C817E
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$header->Class_Terminate();
unset($header);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
