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

class clsGridproducts { //products class @9-0F28B2EA

//Variables @9-33F76C6B

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

//Class_Initialize Event @9-BF8F9066
    function clsGridproducts()
    {
        global $FileName;
        $this->ComponentName = "products";
        $this->Visible = True;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid products";
        $this->ds = new clsproductsDataSource();
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

        $this->product_header = new clsControl(ccsLabel, "product_header", "product_header", ccsText, "", CCGetRequestParam("product_header", ccsGet));
        $this->product_desc = new clsControl(ccsLabel, "product_desc", "product_desc", ccsText, "", CCGetRequestParam("product_desc", ccsGet));
    }
//End Class_Initialize Event

//Initialize Method @9-03626367
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->ds->PageSize = $this->PageSize;
        $this->ds->AbsolutePage = $this->PageNumber;
        $this->ds->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @9-3C7CB6FD
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
                $this->product_header->SetValue($this->ds->product_header->GetValue());
                $this->product_desc->SetValue($this->ds->product_desc->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->product_header->Show();
                $this->product_desc->Show();
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

//GetErrors Method @9-5AA0799D
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->product_header->Errors->ToString();
        $errors .= $this->product_desc->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End products Class @9-FCB6E20C

class clsproductsDataSource extends clsDBskybroadcast {  //productsDataSource Class @9-15841996

//DataSource Variables @9-82B401F3
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $product_header;
    var $product_desc;
//End DataSource Variables

//DataSourceClass_Initialize Event @9-9C6BACCA
    function clsproductsDataSource()
    {
        $this->ErrorBlock = "Grid products";
        $this->Initialize();
        $this->product_header = new clsField("product_header", ccsText, "");
        $this->product_desc = new clsField("product_desc", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @9-FABB2E4B
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "product_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @9-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @9-2E76A08E
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM products";
        $this->SQL = "SELECT *  " .
        "FROM products";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @9-223A65CE
    function SetValues()
    {
		$lang = CCGetSession("lang");
		$sLang = "";
		if ($lang == "russian") $sLang = "_r";
		if ($lang == "arabic") $sLang = "_a";

        $this->product_header->SetDBValue($this->f("product_header" . $sLang));
        $this->product_desc->SetDBValue($this->f("product_desc" . $sLang));
    }
//End SetValues Method

} //End productsDataSource Class @9-FCB6E20C

//Include Page implementation @5-C6940442
include_once(RelativePath . "/subfooter.php");
//End Include Page implementation

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-C6C12177
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

$FileName = "products.php";
$Redirect = "";
$TemplateFileName = "products.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-AA1D858D
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$subheader = new clssubheader("");
$subheader->BindEvents();
$subheader->Initialize();
$products = new clsGridproducts();
$subfooter = new clssubfooter("");
$subfooter->BindEvents();
$subfooter->Initialize();
$footer = new clsfooter("");
$footer->BindEvents();
$footer->Initialize();
$products->Initialize();

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

//Go to destination page @1-B6AD172B
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $subheader->Class_Terminate();
    unset($subheader);
    unset($products);
    $subfooter->Class_Terminate();
    unset($subfooter);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-F6967C92
$subheader->Show("subheader");
$products->Show();
$subfooter->Show("subfooter");
$footer->Show("footer");
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-0937D9A7
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$subheader->Class_Terminate();
unset($subheader);
unset($products);
$subfooter->Class_Terminate();
unset($subfooter);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
