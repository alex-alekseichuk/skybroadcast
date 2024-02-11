<?php
class clsHeader { //Header class @1-CC982CB1

//Variables @1-D089C8BE
    var $FileName = "";
    var $Redirect = "";
    var $Tpl = "";
    var $TemplateFileName = "";
    var $BlockToParse = "";
    var $ComponentName = "";

    // Events;
    var $CCSEvents = "";
    var $CCSEventResult = "";
    var $RelativePath;
    var $Visible;
//End Variables

//Class_Initialize Event @1-A50CC4DA
    function clsHeader($RelativePath)
    {
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->FileName = "Header.php";
        $this->Redirect = "";
        $this->TemplateFileName = "Header.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "";
        if($this->Visible)
        {

            // Create Components
            $this->downloads_list = new clsControl(ccsLink, "downloads_list", "downloads_list", ccsText, "", CCGetRequestParam("downloads_list", ccsGet));
            $this->downloads_list->Page = $this->RelativePath . "downloads_list.php";
            $this->news_list = new clsControl(ccsLink, "news_list", "news_list", ccsText, "", CCGetRequestParam("news_list", ccsGet));
            $this->news_list->Page = $this->RelativePath . "news_list.php";
            $this->products_list = new clsControl(ccsLink, "products_list", "products_list", ccsText, "", CCGetRequestParam("products_list", ccsGet));
            $this->products_list->Page = $this->RelativePath . "products_list.php";
            $this->services_list = new clsControl(ccsLink, "services_list", "services_list", ccsText, "", CCGetRequestParam("services_list", ccsGet));
            $this->services_list->Page = $this->RelativePath . "services_list.php";
            $this->settings_list = new clsControl(ccsLink, "settings_list", "settings_list", ccsText, "", CCGetRequestParam("settings_list", ccsGet));
            $this->settings_list->Page = $this->RelativePath . "settings_list.php";
        }
    }
//End Class_Initialize Event

//Class_Terminate Event @1-A3749DF6
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload");
    }
//End Class_Terminate Event

//BindEvents Method @1-236CCD5D
    function BindEvents()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize");
    }
//End BindEvents Method

//Operations Method @1-7E2A14CF
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
    }
//End Operations Method

//Initialize Method @1-EDD74DD5
    function Initialize()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView");
        if(!$this->Visible)
            return "";
    }
//End Initialize Method

//Show Method @1-E54AA79D
    function Show($Name)
    {
        global $Tpl;
        $block_path = $Tpl->block_path;
        $Tpl->LoadTemplate("/" . $this->TemplateFileName, $Name, $this->TemplateEncoding);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow");
        if(!$this->Visible)
            return "";
        $Tpl->block_path = $Tpl->block_path . "/" . $Name;
        $this->downloads_list->Show();
        $this->news_list->Show();
        $this->products_list->Show();
        $this->services_list->Show();
        $this->settings_list->Show();
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($Name, $Tpl->GetVar($Name));
    }
//End Show Method

} //End Header Class @1-FCB6E20C


?>
