<?php
//Include Common Files @1-5471E0F2
define("RelativePath", ".");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
  
//End Include Common Files

//Include Page implementation @20-294DAD93
include_once(RelativePath . "/menu.php");
//End Include Page implementation

class clsGridservices { //services class @2-B0385B47

//Variables @2-27B94DA9

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
    var $Sorter_service_header;
    var $Sorter_service_desc;
    var $Sorter_service_price;
    var $Sorter_service_speed;
    var $Navigator;
//End Variables

//Class_Initialize Event @2-E05AF07B
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
            $this->PageSize = 20;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        $this->SorterName = CCGetParam("servicesOrder", "");
        $this->SorterDirection = CCGetParam("servicesDir", "");

        $this->Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet));
        $this->Link2 = new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet));
        $this->Link3 = new clsControl(ccsLink, "Link3", "Link3", ccsText, "", CCGetRequestParam("Link3", ccsGet));
        $this->service_header = new clsControl(ccsLink, "service_header", "service_header", ccsText, "", CCGetRequestParam("service_header", ccsGet));
        $this->service_desc = new clsControl(ccsLabel, "service_desc", "service_desc", ccsText, "", CCGetRequestParam("service_desc", ccsGet));
        $this->service_price = new clsControl(ccsLabel, "service_price", "service_price", ccsFloat, "", CCGetRequestParam("service_price", ccsGet));
        $this->service_speed = new clsControl(ccsLabel, "service_speed", "service_speed", ccsFloat, "", CCGetRequestParam("service_speed", ccsGet));
        $this->Sorter_service_header = new clsSorter($this->ComponentName, "Sorter_service_header", $FileName);
        $this->Sorter_service_desc = new clsSorter($this->ComponentName, "Sorter_service_desc", $FileName);
        $this->Sorter_service_price = new clsSorter($this->ComponentName, "Sorter_service_price", $FileName);
        $this->Sorter_service_speed = new clsSorter($this->ComponentName, "Sorter_service_speed", $FileName);
        $this->services_Insert = new clsControl(ccsLink, "services_Insert", "services_Insert", ccsText, "", CCGetRequestParam("services_Insert", ccsGet));
        $this->services_Insert->Parameters = CCGetQueryString("QueryString", Array("service_id", "ccsForm"));
        $this->services_Insert->Page = "services_maint.php";
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

//Show Method @2-7ABD9322
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
                $this->Link1->Parameters = CCAddParam($this->Link1->Parameters, "service_id", $this->ds->f("service_id"));
                $this->Link1->Page = "services_maint.php";
                $this->Link2->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link2->Parameters = CCAddParam($this->Link2->Parameters, "service_id", $this->ds->f("service_id"));
                $this->Link2->Page = "services_maint_ar.php";
                $this->Link3->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link3->Parameters = CCAddParam($this->Link3->Parameters, "service_id", $this->ds->f("service_id"));
                $this->Link3->Page = "services_maint_ru.php";
                $this->service_header->SetValue($this->ds->service_header->GetValue());
                $this->service_header->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->service_header->Parameters = CCAddParam($this->service_header->Parameters, "service_id", $this->ds->f("service_id"));
                $this->service_header->Page = "services_maint.php";
                $this->service_desc->SetValue($this->ds->service_desc->GetValue());
                $this->service_price->SetValue($this->ds->service_price->GetValue());
                $this->service_speed->SetValue($this->ds->service_speed->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->Link1->Show();
                $this->Link2->Show();
                $this->Link3->Show();
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

        $errors = $this->GetErrors();
        if(strlen($errors))
        {
            $Tpl->replaceblock("", $errors);
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->Navigator->PageNumber = $this->ds->AbsolutePage;
        $this->Navigator->TotalPages = $this->ds->PageCount();
        $this->Sorter_service_header->Show();
        $this->Sorter_service_desc->Show();
        $this->Sorter_service_price->Show();
        $this->Sorter_service_speed->Show();
        $this->services_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @2-8CB7D850
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->Link1->Errors->ToString();
        $errors .= $this->Link2->Errors->ToString();
        $errors .= $this->Link3->Errors->ToString();
        $errors .= $this->service_header->Errors->ToString();
        $errors .= $this->service_desc->Errors->ToString();
        $errors .= $this->service_price->Errors->ToString();
        $errors .= $this->service_speed->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End services Class @2-FCB6E20C

class clsservicesDataSource extends clsDBskybroadcast {  //servicesDataSource Class @2-CF0139A5

//DataSource Variables @2-9439A6FF
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

//DataSourceClass_Initialize Event @2-632C63BC
    function clsservicesDataSource()
    {
        $this->ErrorBlock = "Grid services";
        $this->Initialize();
        $this->service_header = new clsField("service_header", ccsText, "");
        $this->service_desc = new clsField("service_desc", ccsText, "");
        $this->service_price = new clsField("service_price", ccsFloat, "");
        $this->service_speed = new clsField("service_speed", ccsFloat, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-000071B1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_service_header" => array("service_header", ""), 
            "Sorter_service_desc" => array("service_desc", ""), 
            "Sorter_service_price" => array("service_price", ""),
            "Sorter_service_speed" => array("service_speed", "")
	));
    }
//End SetOrder Method

//Prepare Method @2-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @2-F5607C7D
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM services";
        $this->SQL = "SELECT services.service_header, services.service_id, services.service_desc, services.service_price, services.service_speed  " .
        "FROM services";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
    }
//End Open Method

//SetValues Method @2-17F47539
    function SetValues()
    {
        $this->service_header->SetDBValue($this->f("service_header"));
        $this->service_desc->SetDBValue($this->f("service_desc"));
        $this->service_price->SetDBValue(trim($this->f("service_price")));
        $this->service_speed->SetDBValue(trim($this->f("service_speed")));
    }
//End SetValues Method

} //End servicesDataSource Class @2-FCB6E20C

//Initialize Page @1-C8394DFB
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

$FileName = "services_list.php";
$Redirect = "";
$TemplateFileName = "services_list.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-5C253EF0
CCSecurityRedirect("admin", "");
//End Authenticate User

//Initialize Objects @1-30A23895
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$menu = new clsmenu("");
$menu->BindEvents();
$menu->Initialize();
$services = new clsGridservices();
$services->Initialize();

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

//Go to destination page @1-DB7C248E
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $menu->Class_Terminate();
    unset($menu);
    unset($services);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-9CD78F02
$menu->Show("menu");
$services->Show();
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-25FDC5F3
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$menu->Class_Terminate();
unset($menu);
unset($services);
unset($Tpl);
//End Unload Page


?>
