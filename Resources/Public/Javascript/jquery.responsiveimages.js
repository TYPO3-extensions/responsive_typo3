/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Georg Paul, opendo GmbH <g.paul@opendo.at>
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
(function($){
	$.fn.extend({
	    getResponsiveSize:function(params){
	        var conf = {};
	        var baseFontSize = parseInt($('body').css('font-size'));	        
	        $.extend(conf, params);
	        
	        return $(this).each(function(){
	        	var $img = $(this);
	        	
				/* remove loading animation from image holder */
				$img.load(function(){
					$img.parents('.responsive-image-holder').addClass('image-loaded');
				});
				
	        	/* img width has been set via css */
	        	/* img height is calculated relatively to it's width */
	        	imgWidth = parseInt($img.css('width')),
	        	imgHeight = Math.round($img.attr('height') * imgWidth / $img.attr('width'));
															
				/* image holder div needs dimensions */				
				/*$img.parents('.csc-textpic').find('*').removeAttr('style');*/
				$img.parents('.responsive-image-holder').css('height', 1 / baseFontSize * imgHeight + 'em');
				
	        	
	        	/* set img attributes, request responsive img from server */
	        	$img.attr('src','?eID=responsive_typo3&img_src=' + $img.data('fullsize') + '&img_width=' + imgWidth + '&img_height=' + imgHeight);
				$img.attr('alt', $img.data('alt'));
				$img.attr('width', imgWidth);
				$img.attr('height', imgHeight);				
				
				/* remove unnecessary attributes */
				$img.removeAttr('data-fullsize data-alt');
										
				/* height is flexible and has to be auto */				
				$img.css('height','auto');
																	
          });                    
	    },
	
		convertToImage:function(params) {
			var conf = {};
			$.extend(conf, params);

			return $(this).each(function(){
				var $noscript = $(this),
				noscriptAttr = this.attributes,												
				$imgTag = $('<img />'),
				str = $noscript.html(),
				originalWidth,
				originalHeight;

				// $noscript.html() is empty in IE:
				// http://stackoverflow.com/questions/4281931/how-to-get-content-of-noscript-in-javascript-in-ie7
				
				
				var temp = str.replace(/(width)\s*=\s*["']([0-9]+)["']\s*(height)\s*=\s*["']([0-9]+)["']/ig, function($0, $1, $2, $3, $4)
				{
					// $0 = original string
					// $1 = either 'width' or 'height'
					// $2 = the value of width or height, depending on above
					// $3 = either 'width' or 'height'
					// $4 = the value of width or height, depending on above
					originalWidth = $2;					
					originalHeight = $4;
					return "";
				});								
				
	        	$.each(noscriptAttr, function(i, attrib){
	        		// add data-width and data-height, regex would be obsolete then
	        		if (attrib.name == 'class' || attrib.name == 'data-fullsize' || attrib.name == 'data-alt') {
	        			$imgTag.attr(attrib.name,attrib.value);
	        		}       			
	        	});	        	  

	        	$imgTag.attr('width', originalWidth);
	        	$imgTag.attr('height', originalHeight);

	        	$noscript.after($imgTag);
	        	$noscript.remove();
			});
		}	
	});
})(jQuery);


function loadResponsiveImages() {
	$('img.responsive-image').getResponsiveSize();	
}

function resize() {
    if (($(document).width()*100)/$(document).data('viewport-width')<95) {
        loadResponsiveImages();
        saveViewportWidth()
    }
}

function saveViewportWidth() {
    $('body').data('viewport-width',$(document).width());
}

$(document).ready(function(){
	$('.responsive-image-holder:visible noscript.responsive-image').convertToImage();
	$('img.responsive-image').getResponsiveSize();	
    saveViewportWidth();

    
	$(window).bind('resize', function(){
        resize()
	});
});
