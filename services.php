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

class clsGridservices { //services class @5-B0385B47

//Variables @5-33F76C6B

    // Public variables
    var $ComponentName;
    var $Visible;
    var $Errors;
    var $ErrorBlock;
    var $ds; var $PageSize;
    var $SorterName = "";
    var $SorterDirection = "";
    var $PageNumber;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    // Grid Controls
    var $StaticControls; var $RowControls;
//End Variables

//Class_Initialize Event @5-259F3B1A
    function clsGridservices()
    {
        global $FileName;
        $this->ComponentName = "services";
        $this->Visible = True;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid services";
        $this->ds = new clsservicesDataSource();
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 10;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));

        $this->service_header = new clsControl(ccsLabel, "service_header", "service_header", ccsText, "", CCGetRequestParam("service_header", ccsGet));
        $this->service_desc = new clsControl(ccsLabel, "service_desc", "service_desc", ccsText, "", CCGetRequestParam("service_desc", ccsGet));
        $this->service_price = new clsControl(ccsLabel, "service_price", "service_price", ccsText, "", CCGetRequestParam("service_price", ccsGet));
        $this->service_speed = new clsControl(ccsLabel, "service_speed", "service_speed", ccsText, "", CCGetRequestParam("service_speed", ccsGet));
    }
//End Class_Initialize Event

//Initialize Method @5-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @5-E07B0421
    function Show()
    {
        global $Tpl;
        if(!$this->Visible) return;

/*
        $ShownRecords = 0;


        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect");


        $this->ds->Prepare();
        $this->ds->Open();

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible) return;

        $GridBlock = "Grid " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $GridBlock;


        $is_next_record = $this->ds->next_record();
        if($is_next_record && $ShownRecords < $this->PageSize)
        {
            do {
                    $this->ds->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->service_header->SetValue($this->ds->service_header->GetValue());
                $this->service_desc->SetValue($this->ds->service_desc->GetValue());
                $this->service_price->SetValue($this->ds->service_price->GetValue());
                $this->service_speed->SetValue($this->ds->service_speed->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->service_header->Show();
                $this->service_desc->Show();
                $this->service_price->Show();
                $this->service_speed->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
                $ShownRecords++;
                $is_next_record = $this->ds->next_record();
            } while ($is_next_record && $ShownRecords < $this->PageSize);
        }
        else // Show NoRecords block if no records are found
        {
            $Tpl->parse("NoRecords", false);
        }
*/
        $errors = $this->GetErrors();
        if(strlen($errors))
        {
            $Tpl->replaceblock("", $errors);
            $Tpl->block_path = $ParentPath;
            return;
        }
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @5-7A5B6887
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->service_header->Errors->ToString();
        $errors .= $this->service_desc->Errors->ToString();
        $errors .= $this->service_price->Errors->ToString();
        $errors .= $this->service_speed->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End services Class @5-FCB6E20C

class clsservicesDataSource extends clsDBskybroadcast {  //servicesDataSource Class @5-CF0139A5

//DataSource Variables @5-1D9ACA15
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $service_header;
    var $service_desc;
    var $service_price;
    var $service_speed;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-7DA9248F
    function clsservicesDataSource()
    {
        $this->ErrorBlock = "Grid services";
        $this->Initialize();
        $this->service_header = new clsField("service_header", ccsText, "");
        $this->service_desc = new clsField("service_desc", ccsText, "");
        $this->service_price = new clsField("service_price", ccsText, "");
        $this->service_speed = new clsField("service_speed", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @5-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @5-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @5-B55B67C2
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM services";
        $this->SQL = "SELECT *  " .
        "FROM services";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @5-1D03F765
    function SetValues()
    {
		$lang = CCGetSession("lang");
		$sLang = "";
		if ($lang == "russian") $sLang = "_r";
		if ($lang == "arabic") $sLang = "_a";

        $this->service_header->SetDBValue($this->f("service_header" . $sLang));
        $this->service_desc->SetDBValue($this->f("service_desc" . $sLang));
        $this->service_price->SetDBValue($this->f("service_price"));
        $this->service_speed->SetDBValue($this->f("service_speed"));
    }
//End SetValues Method

} //End servicesDataSource Class @5-FCB6E20C

//Include Page implementation @8-C6940442
include_once(RelativePath . "/subfooter.php");
//End Include Page implementation

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-37CE9793
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

$FileName = "services.php";
$Redirect = "";
$TemplateFileName = "services.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-4BCC3C79
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$subheader = new clssubheader("");
$subheader->BindEvents();
$subheader->Initialize();
$services = new clsGridservices();
$subfooter = new clssubfooter("");
$subfooter->BindEvents();
$subfooter->Initialize();
$footer = new clsfooter("");
$footer->BindEvents();
$footer->Initialize();
$services->Initialize();

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

//Go to destination page @1-FD15E166
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $subheader->Class_Terminate();
    unset($subheader);
    unset($services);
    $subfooter->Class_Terminate();
    unset($subfooter);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-D56016DD
$subheader->Show("subheader");
$services->Show();
$subfooter->Show("subfooter");
$footer->Show("footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-9E7884FA
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$subheader->Class_Terminate();
unset($subheader);
unset($services);
$subfooter->Class_Terminate();
unset($subfooter);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
