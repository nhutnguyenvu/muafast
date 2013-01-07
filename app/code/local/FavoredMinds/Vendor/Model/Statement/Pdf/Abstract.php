<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

abstract class FavoredMinds_Vendor_Model_Statement_Pdf_Abstract extends Varien_Object
{
    protected $_rgb = array();
    protected $_moveStack = array();
    protected $_imageCache = array();
    protected $_pageWidth = 11;
    protected $_pageHeight = 8.5;
    protected $_lineHeight = 1.3;
    protected $_marginBottom = 1;

    public function addPage()
    {
        $pdf = $this->getPdf();

        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
        $pdf->pages[] = $page;

        $this->x = 0;
        $this->y = $page->getHeight();
        $this->xdpi = $page->getWidth()/$this->_pageWidth;
        $this->ydpi = $page->getHeight()/$this->_pageHeight;

        $this->setPage($page)
            ->fillColor(0)
            ->lineColor(0)
            ->setUnits('inch')
            ->setAlign('left')
            ->setLineHeight($this->_lineHeight)
            ->font('normal', 10)
            ->setMarginBottom($this->_marginBottom)
        ;

        return $this;
    }

    public function checkPageOverflow($add=0, $headerMethod='insertPageHeader')
    {
        if (strtoupper($this->getUnits())=='INCH') {
            $marginBottom = $this->xdpi*($add+$this->getMarginBottom());
        }
        if ($this->y<=$marginBottom) {
            $this->addPage();
            $this->$headerMethod();
        }
        return $this;
    }

    public function addPageNumbers($x, $y)
    {
        $pdf = $this->getPdf();
        $i = 0;
        $n = sizeof($pdf->pages);
        $_helper = Mage::helper('vendor');
        foreach ($pdf->pages as $page) {
            $i++;
            $this->setPage($page)
                ->move($x, $y)->text($_helper->__('Page %s of %s', $i, $n));
        }
        return $this;
    }

    public function move($x=0, $y=0, $units=null)
    {
        $page = $this->getPage();
        $units = strtoupper(!is_null($units) ? $units : $this->getUnits());
        switch ($units) {
        case 'INCH':
            if (!is_null($x)) $this->x = $this->xdpi*$x;
            if (!is_null($y)) $this->y = $page->getHeight()-$this->ydpi*$y;
            break;

        case 'POINT':
            if (!is_null($x)) $this->x = $x;
            if (!is_null($y)) $this->y = $page->getHeight()-$y;
        }
        return $this;
    }

    public function moveRel($x=0, $y=0, $units=null)
    {
        $page = $this->getPage();
        $units = strtoupper(!is_null($units) ? $units : $this->getUnits());
        switch ($units) {
        case 'INCH':
            $this->x += $this->xdpi*$x;
            $this->y -= $this->ydpi*$y;
            break;

        case 'POINT':
            $this->x += $x;
            $this->y -= $y;
        }
        return $this;
    }

    public function movePush()
    {
        array_push($this->_moveStack, array($this->x, $this->y));
        return $this;
    }

    public function movePop($x=0, $y=0, $units=null)
    {
        $p = array_pop($this->_moveStack);
        $this->x = $p[0];
        $this->y = $p[1];
        $this->moveRel($x, $y, $units);
        return $this;
    }

    public function font($type, $size=null)
    {
        switch (strtoupper($type)) {
        case 'NORMAL':
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertineC_Re-2.8.0.ttf'); 
            break;
        case 'BOLD':
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf'); 
            break;
        case 'ITALIC':
            $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf'); 
            break;
        default:
            Mage::throwException('Invalid font type');
        }
        $this->setFont($font);
        if ($size) {
            $this->setFontSize($size);
        }
        $this->getPage()->setFont($font, $this->getFontSize());
        return $this;
    }

    public function fontSize($size)
    {
        $this->setFontSize($size);
        $this->getPage()->setFont($this->getFont(), $size);
        return $this;
    }

    public function text($text, $moveTo=null, $maxLength=null)
    {
        $page = $this->getPage();
        $origLines = explode("\n", $text);
        $lines = array();
        $widths = array();
        foreach ($origLines as $line) {
            $line = trim($line);
            if ($maxLength) {
                while (strlen($line)>$maxLength) {
                    $cutLine = substr($line, 0, $maxLength);
                    $cutoff = strrpos($cutLine, ' ');
                    if ($cutoff===false) {
                        $cutoff = $maxLength;
                    }
                    $lines[] = trim(substr($line, 0, $cutoff));
                    $widths[] = $this->getTextWidth($line);
                    $line = trim(substr($line, $cutoff));
                }
            }
            if (strlen($line)>0) {
                $lines[] = $line;
                $widths[] = $this->getTextWidth($line);
            }
        }
        $lineHeight = $this->getFontSize()*($this->getLineHeight() ? $this->getLineHeight() : 1.3);
        $align = strtoupper($this->getAlign());
        $t = $this->y;
        $this->movePush();
        foreach ($lines as $i=>$line) {
            switch ($align) {
            case 'RIGHT':
                $offset = $widths[$i];
                break;
            case 'CENTER':
                $offset = $widths[$i]/2;
                break;
            default:
                $offset = 0;
            }
            $page->drawText($line, $this->x-$offset, $this->y-$this->getFontSize(), 'UTF-8');
            $this->moveRel(0, $lineHeight, 'point');
        }
        $height = $t-$this->y;
        $this->setMaxHeight(max($this->getMaxHeight(), $height));
        switch (strtoupper($moveTo)) {
        case 'RIGHT':
            $this->movePop(array_reduce($widths, 'max'), 0, 'point');
            break;
        case 'DOWN':
            $this->movePop(0, $height, 'point');
            break;
        default:
            $this->movePop();
        }
        return $this;
    }

    public function price($amount, $moveTo=null, $maxLength=null)
    {
        $this->text(Mage::helper('core')->formatPrice($amount, false), $moveTo, $maxLength);
        return $this;
    }

    public function getTextWidth($string)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $font = $this->getFont();
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $this->getFontSize();
        return $stringWidth;
    }

    public function image($path, $w, $h)
    {
        if (!is_file($path)) {
            return;
        }

        $l = $this->x;
        $t = $this->y;
        $this->movePush()->moveRel($w, $h);

        if (empty($this->_imageCache[$path])) {
            $this->_imageCache[$path] = Zend_Pdf_Image::imageWithPath($path);
        }
        $this->getPage()->drawImage($this->_imageCache[$path], $l, $this->y, $this->x, $t);
        $this->movePop();
        return $this;
    }

    public function rectangle($w, $h, $fillColor=null, $lineColor=null)
    {
        $l = $this->x;
        $t = $this->y;
        $this->movePush()->moveRel($w, $h);
        if (!is_null($fillColor)) {
            $oldFillColor = $this->getFillColor();
            $this->fillColor($fillColor);
        }
        if (!is_null($lineColor)) {
            $oldLineColor = $this->getLineColor();
            $this->lineColor($lineColor);
        }

        $this->getPage()->drawRectangle($l, $t, $this->x, $this->y);

        if (isset($oldFillColor)) {
            $this->fillColor($oldFillColor);
        }
        if (isset($oldLineColor)) {
            $this->lineColor($oldLineColor);
        }
        $this->movePop();
        return $this;
    }

    public function line($w, $h, $lineColor=null)
    {
        $l = $this->x;
        $t = $this->y;
        $this->movePush()->moveRel($w, $h);
        if (!is_null($lineColor)) {
            $oldLineColor = $this->getLineColor();
            $this->lineColor($lineColor);
        }

        $this->getPage()->drawLine($l, $t, $this->x, $this->y);

        if (isset($oldLineColor)) {
            $this->lineColor($oldLineColor);
        }
        $this->movePop();
        return $this;
    }

    public function colorValue($c)
    {
        if (!is_array($c)) {
            $c = array($c, $c, $c);
        }
        $key = join('-', $c);
        if (empty($this->_rgb[$key])) {
            $this->_rgb[$key] = new Zend_Pdf_Color_Rgb($c[0], $c[1], $c[2]);
        }
        return $this->_rgb[$key];
    }

    public function fillColor($color)
    {
        $this->setFillColor($color);
        $this->getPage()->setFillColor($this->colorValue($color));
        return $this;
    }

    public function lineColor($color)
    {
        $this->setLineColor($color);
        $this->getPage()->setLineColor($this->colorValue($color));
        return $this;
    }
}