<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @17-294DAD93
include_once(RelativePath . "/menu.php");
//End Include Page implementation

class clsGridsettings { //settings class @2-6FCEC3C5

//Variables @2-0C3BE392

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

    // Grid Controls
    var $StaticControls; var $RowControls;
    var $Sorter_setting_header;
    var $Sorter_setting_desc;
    var $Navigator;
//End Variables

//Class_Initialize Event @2-93A329C7
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
            $this->PageSize = 20;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        $this->SorterName = CCGetParam("settingsOrder", "");
        $this->SorterDirection = CCGetParam("settingsDir", "");

        $this->Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet));
        $this->Link2 = new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet));
        $this->Link3 = new clsControl(ccsLink, "Link3", "Link3", ccsText, "", CCGetRequestParam("Link3", ccsGet));
        $this->setting_header = new clsControl(ccsLink, "setting_header", "setting_header", ccsText, "", CCGetRequestParam("setting_header", ccsGet));
        $this->setting_desc = new clsControl(ccsLabel, "setting_desc", "setting_desc", ccsText, "", CCGetRequestParam("setting_desc", ccsGet));
        $this->Sorter_setting_header = new clsSorter($this->ComponentName, "Sorter_setting_header", $FileName);
        $this->Sorter_setting_desc = new clsSorter($this->ComponentName, "Sorter_setting_desc", $FileName);
        $this->settings_Insert = new clsControl(ccsLink, "settings_Insert", "settings_Insert", ccsText, "", CCGetRequestParam("settings_Insert", ccsGet));
        $this->settings_Insert->Parameters = CCGetQueryString("QueryString", Array("setting_id", "ccsForm"));
        $this->settings_Insert->Page = "settings_maint.php";
        $this->Navigator = new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpSimple);
    }
//End Class_Initialize Event

//Initialize Method @2-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @2-3EACE8C6
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
                $this->Link1->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link1->Parameters = CCAddParam($this->Link1->Parameters, "setting_id", $this->ds->f("setting_id"));
                $this->Link1->Page = "settings_maint.php";
                $this->Link2->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link2->Parameters = CCAddParam($this->Link2->Parameters, "setting_id", $this->ds->f("setting_id"));
                $this->Link2->Page = "settings_maint_ar.php";
                $this->Link3->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link3->Parameters = CCAddParam($this->Link3->Parameters, "setting_id", $this->ds->f("setting_id"));
                $this->Link3->Page = "settings_maint_ru.php";
                $this->setting_header->SetValue($this->ds->setting_header->GetValue());
                $this->setting_header->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->setting_header->Parameters = CCAddParam($this->setting_header->Parameters, "setting_id", $this->ds->f("setting_id"));
                $this->setting_header->Page = "settings_maint.php";
                $this->setting_desc->SetValue($this->ds->setting_desc->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->Link1->Show();
                $this->Link2->Show();
                $this->Link3->Show();
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
        $this->Navigator->PageNumber = $this->ds->AbsolutePage;
        $this->Navigator->TotalPages = $this->ds->PageCount();
        $this->Sorter_setting_header->Show();
        $this->Sorter_setting_desc->Show();
        $this->settings_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @2-36D2EEDB
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->Link1->Errors->ToString();
        $errors .= $this->Link2->Errors->ToString();
        $errors .= $this->Link3->Errors->ToString();
        $errors .= $this->setting_header->Errors->ToString();
        $errors .= $this->setting_desc->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End settings Class @2-FCB6E20C

class clssettingsDataSource extends clsDBskybroadcast {  //settingsDataSource Class @2-ECD3A02F

//DataSource Variables @2-6A08C10A
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

//DataSourceClass_Initialize Event @2-9664BE9B
    function clssettingsDataSource()
    {
        $this->ErrorBlock = "Grid settings";
        $this->Initialize();
        $this->setting_header = new clsField("setting_header", ccsText, "");
        $this->setting_desc = new clsField("setting_desc", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-4A0CC171
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_setting_header" => array("setting_header", ""), 
            "Sorter_setting_desc" => array("setting_desc", "")));
    }
//End SetOrder Method

//Prepare Method @2-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @2-675F6A4D
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM settings";
        $this->SQL = "SELECT settings.setting_header, settings.setting_id, settings.setting_desc  " .
        "FROM settings";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
    }
//End Open Method

//SetValues Method @2-8D44CF7E
    function SetValues()
    {
        $this->setting_header->SetDBValue($this->f("setting_header"));
        $this->setting_desc->SetDBValue($this->f("setting_desc"));
    }
//End SetValues Method

} //End settingsDataSource Class @2-FCB6E20C

//Initialize Page @1-570F9423
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

$FileName = "settings_list.php";
$Redirect = "";
$TemplateFileName = "settings_list.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-5C253EF0
CCSecurityRedirect("admin", "");
//End Authenticate User

//Initialize Objects @1-6BD9EB2F
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$menu = new clsmenu("");
$menu->BindEvents();
$menu->Initialize();
$settings = new clsGridsettings();
$settings->Initialize();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");

$Charset = $Charset ? $Charset : $TemplateEncoding;
if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-BA4209C8
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate(TemplatePath . $TemplateFileName, "main", $TemplateEncoding);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-F2B7AC12
$menu->Operations();
//End Execute Components

//Go to destination page @1-C1021E83
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $menu->Class_Terminate();
    unset($menu);
    unset($settings);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-919937A5
$menu->Show("menu");
$settings->Show();
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-4074D8A6
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$menu->Class_Terminate();
unset($menu);
unset($settings);
unset($Tpl);
//End Unload Page


?>
