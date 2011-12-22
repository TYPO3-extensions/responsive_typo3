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

$gifbuilder = t3lib_div::makeInstance('tslib_gifBuilder');
$gifbuilder->init();

$image = t3lib_div::makeInstance('Tx_Responsivetypo3_Domain_Image');
$image->setGifbuilder($gifbuilder);
$image->setTargetHeight(t3lib_div::_GP('img_height'));
$image->setTargetWidth(t3lib_div::_GP('img_width'));
$image->setSourcepath(t3lib_div::_GP('img_src'));
$imagePath = $image->getImage();

$imageInfo = getimagesize($imagePath);
header('Content-Type: ' . $imageInfo['mime']);
header("Cache-Control: public, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));
readfile($imagePath);
?>