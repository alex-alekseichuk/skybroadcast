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

class clsGridsettings { //settings class @6-6FCEC3C5

//Variables @6-33F76C6B

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

//Class_Initialize Event @6-6B74A405
    function clsGridsettings()
    {
        global $FileName;
        $this->ComponentName = "settings";
        $this->Visible = True;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid settings";
        $this->ds = new clssettingsDataSource();
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

        $this->setting_header = new clsControl(ccsLabel, "setting_header", "setting_header", ccsText, "", CCGetRequestParam("setting_header", ccsGet));
        $this->setting_desc = new clsControl(ccsLabel, "setting_desc", "setting_desc", ccsText, "", CCGetRequestParam("setting_desc", ccsGet));
    }
//End Class_Initialize Event

//Initialize Method @6-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @6-D3AC9A6C
    function Show()
    {
        global $Tpl;
        if(!$this->Visible) return;

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
                $this->setting_header->SetValue($this->ds->setting_header->GetValue());
                $this->setting_desc->SetValue($this->ds->setting_desc->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->setting_header->Show();
                $this->setting_desc->Show();
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

//GetErrors Method @6-99C98349
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->setting_header->Errors->ToString();
        $errors .= $this->setting_desc->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End settings Class @6-FCB6E20C

class clssettingsDataSource extends clsDBskybroadcast {  //settingsDataSource Class @6-ECD3A02F

//DataSource Variables @6-6A08C10A
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $setting_header;
    var $setting_desc;
//End DataSource Variables

//DataSourceClass_Initialize Event @6-9664BE9B
    function clssettingsDataSource()
    {
        $this->ErrorBlock = "Grid settings";
        $this->Initialize();
        $this->setting_header = new clsField("setting_header", ccsText, "");
        $this->setting_desc = new clsField("setting_desc", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @6-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @6-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @6-6343BF22
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM settings";
        $this->SQL = "SELECT *  " .
        "FROM settings";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @6-8D44CF7E
    function SetValues()
    {
		$lang = CCGetSession("lang");
		$sLang = "";
		if ($lang == "russian") $sLang = "_r";
		if ($lang == "arabic") $sLang = "_a";

        $this->setting_header->SetDBValue($this->f("setting_header" . $sLang));
        $this->setting_desc->SetDBValue($this->f("setting_desc" . $sLang));
    }
//End SetValues Method

} //End settingsDataSource Class @6-FCB6E20C

//Include Page implementation @5-C6940442
include_once(RelativePath . "/subfooter.php");
//End Include Page implementation

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-2E6E9A72
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

$FileName = "settings.php";
$Redirect = "";
$TemplateFileName = "settings.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-CEA06702
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$subheader = new clssubheader("");
$subheader->BindEvents();
$subheader->Initialize();
$settings = new clsGridsettings();
$subfooter = new clssubfooter("");
$subfooter->BindEvents();
$subfooter->Initialize();
$footer = new clsfooter("");
$footer->BindEvents();
$footer->Initialize();
$settings->Initialize();

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

//Go to destination page @1-ADE93BBF
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $subheader->Class_Terminate();
    unset($subheader);
    unset($settings);
    $subfooter->Class_Terminate();
    unset($subfooter);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-B870DB9F
$subheader->Show("subheader");
$settings->Show();
$subfooter->Show("subfooter");
$footer->Show("footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-F33C55A7
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$subheader->Class_Terminate();
unset($subheader);
unset($settings);
$subfooter->Class_Terminate();
unset($subfooter);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
