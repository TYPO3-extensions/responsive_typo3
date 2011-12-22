<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Philipp Bergsmann, opendo GmbH <p.bergsmann@opendo.at>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * Class which processes an image
 *
 * @author	Philipp Bergsmann, opendo GmbH <p.bergsmann@opendo.at>
 * @package TYPO3
 * @subpackage responsive_typo3
 */
class Tx_Responsivetypo3_Domain_Image
{
    /**
     * requested image-width
     *
     * @var int
     */
    protected $targetWidth = 0;

    /**
     * requested image-height
     *
     * @var int
     */
    protected $targetHeight = 0;

    /**
     * @var int
     */
    protected $resizedHeight = 0;

    /**
     * @var int
     */
    protected $resizedWidth = 0;

    /**
     * @var string
     */
    protected $resizedPath = '';

    /**
     * path of the original image-file
     *
     * @var string
     */
    protected $sourcePath = '';

    /**
     * Is concatenated to the size. Possible values: "m", "c", ""
     *
     * @var string
     */
    protected $resizeMethod = 'm';

    /**
     * Valid resize-methods
     *
     * @var array
     */
    protected $resizeMethods = array('c','m');

    /**
     * @var tslib_gifBuilder
     */
    protected $gifbuilder = NULL;

    /**
     * @var bool
     */
    protected $sanitize = TRUE;

    /**
     * @var int
     */
    protected $percentage = 5;

    /**
     * @var int
     */
    protected $maxSize = 2500;

    /**
     * initializes the configuration and sanitizes the
     * requested image-sizes
     */
    public function init() {
        $this->setConfiguration();
        $this->sanitizeTargetDimensions();
    }

    /**
     * Resizes the image and returns the path
     *
     * @return string
     */
    public function getImage() {
        $this->init();

        $newImage = $this->getGifbuilder()->imageMagickConvert(
                trim($this->getSourcePath(),'/'),
                '',
                $this->getTargetWidth() . $this->getResizemethod(),
                $this->getTargetHeight() . $this->getResizemethod()
            );

        return $newImage[3];
    }

    /**
     * Sanitizes the width and the hight depending on the
     * percentage set in the extensionmanager. This should
     * prevent you from cache-flooding
     */
    protected function sanitizeTargetDimensions() {
        if ($this->sanitize) {
            $sourceSize = getimagesize(PATH_site . $this->getSourcePath());

            $sanitizedWidth = $this->getTargetWidth();
            $sanitizedHeight = $this->getTargetHeight();

            $step = $sourceSize[0] * ($this->percentage/100);

            foreach (range($step,$this->maxSize,$step) as $version) {
                if ($this->getTargetWidth() <= $version || ($version+$step) > $this->maxSize) {
                    $sanitizedWidth = $version;
                    $scale = ($sanitizedWidth * 100) / $sourceSize[0];
                    $sanitizedHeight = $sourceSize[1] * $scale;
                    break;
                }
            }

            $this->setTargetWidth($sanitizedWidth);
            $this->setTargetHeight($sanitizedHeight);
        }
    }

    /**
     * replaces the default values with the ones from the
     * extension manager - if set
     */
    protected function setConfiguration() {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['responsive_typo3']);
        $this->maxSize = (!empty($extConf['maxSize'])) ? $extConf['maxSize'] : $this->maxSize;
        $this->percentage = (!empty($extConf['resizePercentage'])) ? $extConf['resizePercentage'] : $this->percentage;
    }

    /**
     * disables spam-flooding protection
     */
    public function disableSizeSanitization() {
        $this->sanitize = false;
    }

    /**
     * @param tslib_gifBuilder $gifbuilder
     */
    public function setGifbuilder(tslib_gifBuilder $gifbuilder) {
        $this->gifbuilder = $gifbuilder;
    }

    /**
     * @return tslib_gifBuilder
     */
    public function getGifbuilder() {
        return $this->gifbuilder;
    }

    /**
     * @param string $src
     */
    public function setSourcePath($src) {
        $this->isValidFile($src);
        $this->sourcePath = $src;
    }

    /**
     * @return string
     */
    public function getSourcePath() {
        return $this->sourcePath;
    }

    /**
     * @param int $width
     */
    public function setTargetWidth($width) {
        $this->targetWidth = (int) $width;
    }

    /**
     * @return int
     */
    public function getTargetWidth() {
        return $this->targetWidth;
    }

    /**
     * @param int $height
     */
    public function setTargetHeight($height) {
        $this->targetHeight = (int) $height;
    }

    /**
     * @return int
     */
    public function getTargetHeight() {
        return $this->targetHeight;
    }

    /**
     * @param string $method
     * @throws Tx_Responsivetypo3_Exception_Resizemethod
     */
    public function setResizemethod($method) {
        if (!in_array($method, $this->resizeMethods)) {
            throw new Tx_Responsivetypo3_Exception_Resizemethod(
                    'Resize-method has to be one of the following: '
                    . implode(', ', $this->resizeMethods)
                );
        }

        $this->resizeMethod = $method;
    }

    /**
     * @return string
     */
    public function getResizemethod() {
        return $this->resizeMethod;
    }

    /**
     * Checks for a valid file-path and valid file-extension
     *
     * @param string $fileName
     * @return void
     * @throws Tx_Responsivetypo3_Exception_Path
     * @throws Tx_Responsivetypo3_Exception_Filetype
     */
    protected function isValidFile($fileName) {
        if (!t3lib_div::validPathStr($fileName)) {
             throw new Tx_Responsivetypo3_Exception_Path('invalid path');
        }

        $fileRef = t3lib_div::split_fileref($fileName);
        $imgFileExt = t3lib_div::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            );

        if (!in_array($fileRef['realFileext'], $imgFileExt)) {
            throw new Tx_Responsivetypo3_Exception_Filetype('invalid file extension: ' . $imgFileExt);
        }
    }
}
