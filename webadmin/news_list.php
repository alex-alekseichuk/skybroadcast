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

class clsGridnews { //news class @2-6436B12B

//Variables @2-995BC1D8

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
    var $Sorter_news_header;
    var $Sorter_news_date;
    var $Navigator;
//End Variables

//Class_Initialize Event @2-55E87B95
    function clsGridnews()
    {
        global $FileName;
        $this->ComponentName = "news";
        $this->Visible = True;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid news";
        $this->ds = new clsnewsDataSource();
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
        $this->SorterName = CCGetParam("newsOrder", "");
        $this->SorterDirection = CCGetParam("newsDir", "");

        $this->LinkEn = new clsControl(ccsLink, "LinkEn", "LinkEn", ccsText, "", CCGetRequestParam("LinkEn", ccsGet));
        $this->Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet));
        $this->Link2 = new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet));
        $this->news_header = new clsControl(ccsLink, "news_header", "news_header", ccsText, "", CCGetRequestParam("news_header", ccsGet));
        $this->news_date = new clsControl(ccsLabel, "news_date", "news_date", ccsText, "", CCGetRequestParam("news_date", ccsGet));
        $this->Sorter_news_header = new clsSorter($this->ComponentName, "Sorter_news_header", $FileName);
        $this->Sorter_news_date = new clsSorter($this->ComponentName, "Sorter_news_date", $FileName);
        $this->news_Insert = new clsControl(ccsLink, "news_Insert", "news_Insert", ccsText, "", CCGetRequestParam("news_Insert", ccsGet));
        $this->news_Insert->Parameters = CCGetQueryString("QueryString", Array("news_id", "ccsForm"));
        $this->news_Insert->Page = "news_maint.php";
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

//Show Method @2-347E522B
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
                $this->LinkEn->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->LinkEn->Parameters = CCAddParam($this->LinkEn->Parameters, "news_id", $this->ds->f("news_id"));
                $this->LinkEn->Page = "news_maint.php";
                $this->Link1->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link1->Parameters = CCAddParam($this->Link1->Parameters, "news_id", $this->ds->f("news_id"));
                $this->Link1->Page = "news_maint_ar.php";
                $this->Link2->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link2->Parameters = CCAddParam($this->Link2->Parameters, "news_id", $this->ds->f("news_id"));
                $this->Link2->Page = "news_maint_ru.php";
                $this->news_header->SetValue($this->ds->news_header->GetValue());
                $this->news_header->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->news_header->Parameters = CCAddParam($this->news_header->Parameters, "news_id", $this->ds->f("news_id"));
                $this->news_header->Page = "news_maint.php";
                $this->news_date->SetValue($this->ds->news_date->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->LinkEn->Show();
                $this->Link1->Show();
                $this->Link2->Show();
                $this->news_header->Show();
                $this->news_date->Show();
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
        $this->Sorter_news_header->Show();
        $this->Sorter_news_date->Show();
        $this->news_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @2-9002FFFA
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->LinkEn->Errors->ToString();
        $errors .= $this->Link1->Errors->ToString();
        $errors .= $this->Link2->Errors->ToString();
        $errors .= $this->news_header->Errors->ToString();
        $errors .= $this->news_date->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End news Class @2-FCB6E20C

class clsnewsDataSource extends clsDBskybroadcast {  //newsDataSource Class @2-8BEA71CD

//DataSource Variables @2-EB8B0478
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $news_header;
    var $news_date;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-57DC589F
    function clsnewsDataSource()
    {
        $this->ErrorBlock = "Grid news";
        $this->Initialize();
        $this->news_header = new clsField("news_header", ccsText, "");
        $this->news_date = new clsField("news_date", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-8D8164F1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_news_header" => array("news_header", ""), 
            "Sorter_news_date" => array("news_date", "")));
    }
//End SetOrder Method

//Prepare Method @2-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @2-8C89989C
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM news";
        $this->SQL = "SELECT news.news_header, news.news_id, news.news_date  " .
        "FROM news";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
    }
//End Open Method

//SetValues Method @2-BADDB8CD
    function SetValues()
    {
        $this->news_header->SetDBValue($this->f("news_header"));
        $this->news_date->SetDBValue($this->f("news_date"));
    }
//End SetValues Method

} //End newsDataSource Class @2-FCB6E20C

//Initialize Page @1-0A00C985
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

$FileName = "news_list.php";
$Redirect = "";
$TemplateFileName = "news_list.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-5C253EF0
CCSecurityRedirect("admin", "");
//End Authenticate User

//Initialize Objects @1-65448D7D
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$menu = new clsmenu("");
$menu->BindEvents();
$menu->Initialize();
$news = new clsGridnews();
$news->Initialize();

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

//Go to destination page @1-AA4EC702
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $menu->Class_Terminate();
    unset($menu);
    unset($news);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-03B5461B
$menu->Show("menu");
$news->Show();
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-941B40CB
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$menu->Class_Terminate();
unset($menu);
unset($news);
unset($Tpl);
//End Unload Page


?>
