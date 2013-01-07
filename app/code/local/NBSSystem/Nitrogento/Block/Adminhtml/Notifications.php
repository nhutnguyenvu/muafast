<?php
/**
 * NBS System
 * 
 */

class NBSSystem_Nitrogento_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    public $filesNotWriteable = array();
    
    /**
     * Check if all files is writeable
     *
     * @return bool
     */
    public function isAllFilesWriteable()
    {
        $res = true;
        $files = array('/index.php','/.htaccess');
        
        foreach ($files as $file) 
        {
            $checkRes = $this->_checkPath($file);
            $res = $res && $checkRes;
        }
        
        return $res;
    }

    /**
     * Check if file or directory is writeable
     *
     * @param   string $path
     * @return  bool
     */
    protected function _checkPath($path)
    {
        $res = true;
        $fullPath = dirname(Mage::getRoot()).$path;
		
        if (!is_writable($fullPath)) 
        {
            $res = false;
        }

        return $res;
    }    
    
    /**
     * Get a list of not writeable files
     *
     * @return  array
     */
    public function getFilesNotWriteable()
    {
        global $filesNotWriteable;

        return $filesNotWriteable;
    } 
 
    /**
     * Check if  files not writeable
     *
     * @return bool
     */
    public function isFilesNotWriteable()
    {
        global $filesNotWriteable;

        $res = true;
        $files = array('/index.php','/.htaccess');
        
        foreach ($files as $file) 
        {
            $checkRes = $this->_checkPath($file);
            if(!$checkRes) { $filesNotWriteable[] = $file; }
            $res = $res && $checkRes;
        }
        
        return $res;
    }   
}