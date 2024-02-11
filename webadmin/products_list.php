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

class clsGridproducts { //products class @2-0F28B2EA

//Variables @2-0822E56E

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
    var $Sorter_product_header;
    var $Sorter_product_desc;
    var $Navigator;
//End Variables

//Class_Initialize Event @2-F5312EAC
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
            $this->PageSize = 20;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        $this->SorterName = CCGetParam("productsOrder", "");
        $this->SorterDirection = CCGetParam("productsDir", "");

        $this->Link1 = new clsControl(ccsLink, "Link1", "Link1", ccsText, "", CCGetRequestParam("Link1", ccsGet));
        $this->Link2 = new clsControl(ccsLink, "Link2", "Link2", ccsText, "", CCGetRequestParam("Link2", ccsGet));
        $this->Link3 = new clsControl(ccsLink, "Link3", "Link3", ccsText, "", CCGetRequestParam("Link3", ccsGet));
        $this->product_header = new clsControl(ccsLink, "product_header", "product_header", ccsText, "", CCGetRequestParam("product_header", ccsGet));
        $this->product_desc = new clsControl(ccsLabel, "product_desc", "product_desc", ccsText, "", CCGetRequestParam("product_desc", ccsGet));
        $this->Sorter_product_header = new clsSorter($this->ComponentName, "Sorter_product_header", $FileName);
        $this->Sorter_product_desc = new clsSorter($this->ComponentName, "Sorter_product_desc", $FileName);
        $this->products_Insert = new clsControl(ccsLink, "products_Insert", "products_Insert", ccsText, "", CCGetRequestParam("products_Insert", ccsGet));
        $this->products_Insert->Parameters = CCGetQueryString("QueryString", Array("product_id", "ccsForm"));
        $this->products_Insert->Page = "products_maint.php";
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

//Show Method @2-7AA2EA27
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
                $this->Link1->Parameters = CCAddParam($this->Link1->Parameters, "product_id", $this->ds->f("product_id"));
                $this->Link1->Page = "products_maint.php";
                $this->Link2->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link2->Parameters = CCAddParam($this->Link2->Parameters, "product_id", $this->ds->f("product_id"));
                $this->Link2->Page = "products_maint_ar.php";
                $this->Link3->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->Link3->Parameters = CCAddParam($this->Link3->Parameters, "product_id", $this->ds->f("product_id"));
                $this->Link3->Page = "products_maint_ru.php";
                $this->product_header->SetValue($this->ds->product_header->GetValue());
                $this->product_header->Parameters = CCGetQueryString("QueryString", Array("ccsForm"));
                $this->product_header->Parameters = CCAddParam($this->product_header->Parameters, "product_id", $this->ds->f("product_id"));
                $this->product_header->Page = "products_maint.php";
                $this->product_desc->SetValue($this->ds->product_desc->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow");
                $this->Link1->Show();
                $this->Link2->Show();
                $this->Link3->Show();
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
        $this->Navigator->PageNumber = $this->ds->AbsolutePage;
        $this->Navigator->TotalPages = $this->ds->PageCount();
        $this->Sorter_product_header->Show();
        $this->Sorter_product_desc->Show();
        $this->products_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->ds->close();
    }
//End Show Method

//GetErrors Method @2-F5BB140F
    function GetErrors()
    {
        $errors = "";
        $errors .= $this->Link1->Errors->ToString();
        $errors .= $this->Link2->Errors->ToString();
        $errors .= $this->Link3->Errors->ToString();
        $errors .= $this->product_header->Errors->ToString();
        $errors .= $this->product_desc->Errors->ToString();
        $errors .= $this->Errors->ToString();
        $errors .= $this->ds->Errors->ToString();
        return $errors;
    }
//End GetErrors Method

} //End products Class @2-FCB6E20C

class clsproductsDataSource extends clsDBskybroadcast {  //productsDataSource Class @2-15841996

//DataSource Variables @2-82B401F3
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

//DataSourceClass_Initialize Event @2-9C6BACCA
    function clsproductsDataSource()
    {
        $this->ErrorBlock = "Grid products";
        $this->Initialize();
        $this->product_header = new clsField("product_header", ccsText, "");
        $this->product_desc = new clsField("product_desc", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-40692342
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_product_header" => array("product_header", ""), 
            "Sorter_product_desc" => array("product_desc", "")));
    }
//End SetOrder Method

//Prepare Method @2-DFF3DD87
    function Prepare()
    {
    }
//End Prepare Method

//Open Method @2-AC30A43F
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect");
        $this->CountSQL = "SELECT COUNT(*)  " .
        "FROM products";
        $this->SQL = "SELECT products.product_header, products.product_id, products.product_desc  " .
        "FROM products";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect");
        $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect");
    }
//End Open Method

//SetValues Method @2-223A65CE
    function SetValues()
    {
        $this->product_header->SetDBValue($this->f("product_header"));
        $this->product_desc->SetDBValue($this->f("product_desc"));
    }
//End SetValues Method

} //End productsDataSource Class @2-FCB6E20C

//Initialize Page @1-04E114C0
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

$FileName = "products_list.php";
$Redirect = "";
$TemplateFileName = "products_list.html";
$BlockToParse = "main";
$TemplateEncoding = "";
$FileEncoding = "";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-5C253EF0
CCSecurityRedirect("admin", "");
//End Authenticate User

//Initialize Objects @1-C8BABE60
$DBskybroadcast = new clsDBskybroadcast();

// Controls
$menu = new clsmenu("");
$menu->BindEvents();
$menu->Initialize();
$products = new clsGridproducts();
$products->Initialize();

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

//Go to destination page @1-D53F7A99
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
    $DBskybroadcast->close();
    header("Location: " . $Redirect);
    $menu->Class_Terminate();
    unset($menu);
    unset($products);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-23DD6B34
$menu->Show("menu");
$products->Show();
$Tpl->PParse("main", false);
//End Show Page

//Unload Page @1-75F2E669
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload");
$DBskybroadcast->close();
$menu->Class_Terminate();
unset($menu);
unset($products);
unset($Tpl);
//End Unload Page


?>
