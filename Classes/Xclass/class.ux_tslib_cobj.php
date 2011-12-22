<?php
class ux_tslib_cObj extends tslib_cObj
{
	function cImage($file, $conf) {

        $theValue = parent::cImage($file, $conf);

        if (!empty($theValue)) {
            $jsImgTagSrc = preg_replace('/.*(src\s*=\s*")(\S*)".*/i', '${2}', $theValue);
            $jsImgTag = $theValue;
            $jsImgTag = preg_replace('/(\s*width\s*=\s*"\S*")|(\s*height\s*=\s*"\S*")/i','',$jsImgTag);
            $jsImgTag = (preg_match('/(class\s*=\s*"\w*)(")/i', $jsImgTag)) ? preg_replace('/(class\s*=\s*"\w*)(")/i', '${1} responsive-image${2}', $jsImgTag) : preg_replace('/(\/>)/i', 'class="responsive-image" ${1}', $jsImgTag);
            $jsImgTag = preg_replace('/alt\=/', 'data-alt=', $jsImgTag);

            $jsImgTag = preg_replace('/(\/>)/i', 'data-fullsize="' . $jsImgTagSrc . '" ${1}', $jsImgTag);
            $NoscriptTag = preg_replace('/img(.*)(\/\>)/i','div class="responsive-image-holder"><noscript${1}>',$jsImgTag);
            $NoscriptTag = preg_replace('/(src\s*=\s*")(\S*)"/i', '', $NoscriptTag);
            return $NoscriptTag . $theValue . '</noscript></div>';
        }

        return '';
	}
}
?>