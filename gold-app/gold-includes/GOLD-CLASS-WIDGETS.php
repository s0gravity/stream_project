<?php
// --- GOLD MEDIA --- //

define('GOLD_BASE', dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/');

class GOLD_WIDGETS extends SkinFunctions {
    var $widgetPath='';
    var $widgetName='';
    public function setWidgetPath($widgetName)
    {
        $this->widgetPath=GOLD_BASE.'gold-app/gold-widgets/'.$widgetName.'/';
        $this->widgetName=$widgetName;
    }
    public function getWidgetPath()
    {
        return $this->widgetPath;
    }
    public function display()
    {
        echo 'Widgets';
    }
    public function run($widgetName)// this function will be called by template function class to display widget
    {
        $this->setWidgetPath($widgetName);
        $this->display();
    }
}
?>