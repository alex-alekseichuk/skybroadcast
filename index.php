<?php
//Include Common Files @1-83C617B7
define("RelativePath", ".");
define("PathToCurrentPage", "/");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

class clsGridnews { //news class @7-6436B12B

//Variables @7-33F76C6B

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

//Class_Initialize Event @7-F2369837
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
            $this->PageSize = 10;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));

        $this->news_date = new clsControl(ccsLabel, "news_date", "news_date", ccsText, "", CCGetRequestParam("news_date", ccsGet));
        $this->news_header = new clsControl(ccsLabel, "news_header", "news_header", ccsText, "", CCGetRequestParam("news_header", ccsGet));
        $this->news_body = new clsControl(ccsLabel, "news_body", "news_body", ccsMemo, "", CCGetRequestParam("news_body", ccsGet));
    }
//End Class_Initialize Event

//Initialize Method @7-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @7-52702694
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
                $this->news_date->SetValue($this->ds->news_date->GetValue());
                $this->news_header->SetValue($this->ds->news_header->GetValue());
                $this->news_body->SetValue($this->ds->news_body->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->news_date->Show();
                $this->news_header->Show();
                $this->news_body->Show();
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

		$lang = CCGetSession("lang");
		$sLang = "en";
		if ($lang == "russian") $sLang = "ru";
		if ($lang == "arabic") $sLang = "ar";
		$Tpl->setvar("home_text", 
			CCGetDBValue(CCBuildSQL("select " . $sLang . " as t from strings", "id='home'", ""), $this->ds)
		);

        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @7-3C82D758
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->news_date->Errors->ToString();
        $errors .= $this->news_header->Errors->ToString();
        $errors .= $this->news_body->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End news Class @7-FCB6E20C

class clsnewsDataSource extends clsDBskybroadcast {  //newsDataSource Class @7-8BEA71CD

//DataSource Variables @7-897768D7
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $news_date;
    var $news_header;
    var $news_body;
//End DataSource Variables

//DataSourceClass_Initialize Event @7-6A24C133
    function clsnewsDataSource()
    {
        $this->ErrorBlock = "Grid news";
        $this->Initialize();
        $this->news_date = new clsField("news_date", ccsText, "");
        $this->news_header = new clsField("news_header", ccsText, "");
        $this->news_body = new clsField("news_body", ccsMemo, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @7-91517485
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "news_id DESC limit 1";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @7-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @7-620D430D
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*) FROM news";
        $this->SQL = "select * from news";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        if ($this->RecordsCount > 0) $this->RecordsCount = 1;
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @7-23DC64A9
    function SetValues()
    {
		$lang = CCGetSession("lang");
		$sLang = "";
		if ($lang == "russian") $sLang = "_r";
		if ($lang == "arabic") $sLang = "_a";

        $this->news_date->SetDBValue($this->f("news_date"));
        $this->news_header->SetDBValue($this->f("news_header".$sLang));
        $this->news_body->SetDBValue($this->f("news_body".$sLang));
    }
//End SetValues Method

} //End newsDataSource Class @7-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation


	$lang = CCGetSession("lang");
	if ($lang == "")
	{
		$lang = "english";
		CCSetSession("lang", $lang);
	}
	if (CCGetParam("lang", "") != "")
	{
		$lang = CCGetParam("lang", "");
		CCSetSession("lang", $lang);
	}
	

//Initialize Page @1-4CE114A5
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

$FileName = "index.php";
$Redirect = "";
$TemplateFileName = "index_.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-BE860AE0
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$news = new clsGridnews();
$footer1 = new clsfooter("");
$footer1->BindEvents();
$footer1->Initialize();
$news->Initialize();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize");

if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-04FA3713
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView");
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate($TemplateFileName, "main", $TemplateEncoding);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow");
//End Initialize HTML Template

//Execute Components @1-20EA64BB
$footer1->Operations();
//End Execute Components

//Go to destination page @1-284FC119
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    unset($news);
    $footer1->Class_Terminate();
    unset($footer1);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-08B1E04B
$news->Show();
$footer1->Show("footer1");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-156480C8
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
unset($news);
$footer1->Class_Terminate();
unset($footer1);
unset($Tpl);
//End Unload Page


?>
