<?php
//Include Common Files @1-83C617B7
define("RelativePath", ".");
define("PathToCurrentPage", "/");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @4-A39D4A7D
include_once(RelativePath . "/subheader.php");
//End Include Page implementation

//Include Page implementation @5-C6940442
include_once(RelativePath . "/subfooter.php");
//End Include Page implementation

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-7EF8ACBE
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

$FileName = "contactus.php";
$Redirect = "";
$TemplateFileName = "contactus.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-E81E4D82

// Controls
$subheader = new clssubheader("");
$subheader->BindEvents();
$subheader->Initialize();
$subfooter = new clssubfooter("");
$subfooter->BindEvents();
$subfooter->Initialize();
$footer = new clsfooter("");
$footer->BindEvents();
$footer->Initialize();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");

if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-04FA3713
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate(PathToCurrentPage . $TemplateFileName, "main", $TemplateEncoding);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-02567312
$subheader->Operations();
$subfooter->Operations();
$footer->Operations();
//End Execute Components

//Go to destination page @1-8684958F
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    header("Location: " . $Redirect);
    $subheader->Class_Terminate();
    unset($subheader);
    $subfooter->Class_Terminate();
    unset($subfooter);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-099E6A43
$subheader->Show("subheader");
$subfooter->Show("subfooter");
$footer->Show("footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-E9737547
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$subheader->Class_Terminate();
unset($subheader);
$subfooter->Class_Terminate();
unset($subfooter);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
