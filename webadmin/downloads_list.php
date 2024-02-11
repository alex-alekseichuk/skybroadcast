<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @16-294DAD93
include_once(RelativePath . "/menu.php");
//End Include Page implementation

class clsGriddownloads { //downloads class @2-7ABFB38B

//Variables @2-459B0490

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
    var $Sorter_download_header;
    var $Sorter_download_url;
    var $Navigator;
//End Variables

//Class_Initialize Event @2-E6CBC6BE
    function clsGriddownloads()
    {
        global $FileName;
        $this->ComponentName = "downloads";
        $this->Visible = True;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid downloads";
        $this->ds = new clsdownloadsDataSource();
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
        $this->SorterName = CCGetParam("downloadsOrder", "");
        $this->SorterDirection = CCGetParam("downloadsDir", "");

        $this->Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet));
        $this->Link2 = new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet));
        $this->Link3 = new clsControl(ccsLink, "Link3", "Link3", ccsText, "", CCGetRequestParam("Link3", ccsGet));
        $this->download_header = new clsControl(ccsLink, "download_header", "download_header", ccsText, "", CCGetRequestParam("download_header", ccsGet));
        $this->download_url = new clsControl(ccsLabel, "download_url", "download_url", ccsText, "", CCGetRequestParam("download_url", ccsGet));
        $this->Sorter_download_header = new clsSorter($this->ComponentName, "Sorter_download_header", $FileName);
        $this->Sorter_download_url = new clsSorter($this->ComponentName, "Sorter_download_url", $FileName);
        $this->downloads_Insert = new clsControl(ccsLink, "downloads_Insert", "downloads_Insert", ccsText, "", CCGetRequestParam("downloads_Insert", ccsGet));
        $this->downloads_Insert->Parameters = CCGetQueryString("QueryString", Array("download_id", "ccsForm"));
        $this->downloads_Insert->Page = "downloads_maint.php";
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

//Show Method @2-115AF4C6
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
                $this->Link1->Parameters = CCAddParam($this->Link1->Parameters, "download_id", $this->ds->f("download_id"));
                $this->Link1->Page = "downloads_maint.php";
                $this->Link2->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link2->Parameters = CCAddParam($this->Link2->Parameters, "download_id", $this->ds->f("download_id"));
                $this->Link2->Page = "downloads_maint_ar.php";
                $this->Link3->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link3->Parameters = CCAddParam($this->Link3->Parameters, "download_id", $this->ds->f("download_id"));
                $this->Link3->Page = "downloads_maint_ru.php";
                $this->download_header->SetValue($this->ds->download_header->GetValue());
                $this->download_header->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->download_header->Parameters = CCAddParam($this->download_header->Parameters, "download_id", $this->ds->f("download_id"));
                $this->download_header->Page = "downloads_maint.php";
                $this->download_url->SetValue($this->ds->download_url->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->Link1->Show();
                $this->Link2->Show();
                $this->Link3->Show();
                $this->download_header->Show();
                $this->download_url->Show();
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
        $this->Sorter_download_header->Show();
        $this->Sorter_download_url->Show();
        $this->downloads_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @2-1FA90669
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->Link1->Errors->ToString();
        $errors .= $this->Link2->Errors->ToString();
        $errors .= $this->Link3->Errors->ToString();
        $errors .= $this->download_header->Errors->ToString();
        $errors .= $this->download_url->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End downloads Class @2-FCB6E20C

class clsdownloadsDataSource extends clsDBskybroadcast {  //downloadsDataSource Class @2-0759839C

//DataSource Variables @2-D4D9459C
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $download_header;
    var $download_url;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-FBF975F8
    function clsdownloadsDataSource()
    {
        $this->ErrorBlock = "Grid downloads";
        $this->Initialize();
        $this->download_header = new clsField("download_header", ccsText, "");
        $this->download_url = new clsField("download_url", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-1F4F2EF4
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "download_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_download_header" => array("download_header", ""), 
            "Sorter_download_url" => array("download_url", "")));
    }
//End SetOrder Method

//Prepare Method @2-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @2-6F17A944
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM downloads";
        $this->SQL = "SELECT downloads.download_header, downloads.download_id, downloads.download_url  " .
        "FROM downloads";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
    }
//End Open Method

//SetValues Method @2-0965562A
    function SetValues()
    {
        $this->download_header->SetDBValue($this->f("download_header"));
        $this->download_url->SetDBValue($this->f("download_url"));
    }
//End SetValues Method

} //End downloadsDataSource Class @2-FCB6E20C

//Initialize Page @1-679109F3
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

$FileName = "downloads_list.php";
$Redirect = "";
$TemplateFileName = "downloads_list.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-E0C1ACC5
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$menu = new clsmenu("");
$menu->BindEvents();
$menu->Initialize();
$downloads = new clsGriddownloads();
$downloads->Initialize();

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

//Go to destination page @1-BAF264D5
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $menu->Class_Terminate();
    unset($menu);
    unset($downloads);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-F8BA33F7
$menu->Show("menu");
$downloads->Show();
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-533902F4
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$menu->Class_Terminate();
unset($menu);
unset($downloads);
unset($Tpl);
//End Unload Page


?>
