$(function() {

	// Fix code blocks so they are as compact as possible
	if (typeof elk_codefix === 'function')
		elk_codefix();

	// Enable the ... page expansion
	$('.expand_pages').expand_pages();

	// Spoiler
	$('.spoilerheader').click(function() {
		$(this).next().children().slideToggle("fast");
	});

	// BBC [img] element toggle for height and width styles of an image.
	$('img').each(function() {
		// Not a resized image? Skip it.
		if ($(this).hasClass('bbc_img resized') === false)
			return true;

		$(this).css({'cursor': 'pointer'});

		// Note to addon authors, if you want to enable your own click events to bbc images
		// you can turn off this namespaced click event with $("img").off("click.elk_bbc")
		$(this).on( "click.elk_bbc", function() {
			var $this = $(this);

			// No saved data, then lets set it to auto
			if ($.isEmptyObject($this.data('bbc_img')))
			{
				$this.data('bbc_img', {
					width: $this.css('width'),
					height: $this.css('height'),
					'max-width': $this.css('max-width'),
					'max-height': $this.css('max-height')
				});
				$this.css({'width': $this.css('width') === 'auto' ? null : 'auto'});
				$this.css({'height': $this.css('height') === 'auto' ? null : 'auto'});

				// Override default css to allow the image to expand fully, add a div to expand in
				$this.css({'max-height': 'none'});
				$this.css({'max-width': '100%'});
				$this.wrap('<div style="overflow:auto;display:inline-block;"></div>');
			}
			else
			{
				// Was clicked and saved, so set it back
				$this.css({'width': $this.data("bbc_img").width});
				$this.css({'height': $this.data("bbc_img").height});
				$this.css({'max-width': $this.data("bbc_img")['max-width']});
				$this.css({'max-height': $this.data("bbc_img")['max-height']});

				// Remove the data
				$this.removeData('bbc_img');

				// Remove the div we added to allow the image to overflow expand in
				$this.unwrap();
				$this.css({'max-width': '100%'});
			}
		});
	});

	$('.reveal_ul').click(function(e) {
		e.preventDefault();
		var id = $(this).attr('data-type');
		$('#' + id).toggleClass('revealed');
		$(this).toggleClass('hilite');
	});

	$('#qrBig').click(function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		$('#' + id).toggleClass('fixedbottom');
		$('#footer_section').toggleClass('bigbottom');
		$(this).toggleClass('i-chevron-collapse');
	});

	$('#mobile_nav li').click(function(e) {
		e.preventDefault();

		$('html, body').animate({
			scrollTop: $("#top_section").offset().top}, 1); 

		$('#mobilmeny').prop('checked', false);
		var id = $(this).attr('data-id');
		var ida = $(this).attr('data-ida');
		var idb = $(this).attr('data-idb');
		
		$('.mpart').hide();
		$('.mpart').removeClass('show_mpart');
		
		if(id){
			$('#' + id).addClass('show_mpart');
		}
		if(ida){
			$('#' + ida).addClass('show_mpart');
		}
		if(idb){
			$('#' + idb).addClass('show_mpart');
		}
		$('#mobile_nav li').removeClass('hilite_cbutton');
		$(this).addClass('hilite_cbutton');
	});

	$('.attachment_image_cont').click(function(e) {
		e.preventDefault();
		$(this).toggleClass('maximise');
	});


});

/**
 * Adds a button to the quick topic moderation after a checkbox is selected
 *
 * @param {string} sButtonStripId
 * @param {boolean} bUseImage
 * @param {object} oOptions
 */
function elk_addButton(sButtonStripId, bUseImage, oOptions)
{
	var oButtonStrip = document.getElementById(sButtonStripId),
		aItems = oButtonStrip.getElementsByTagName('span');

	// Remove the 'last' class from the last item.
	if (aItems.length > 0)
	{
		var oLastSpan = aItems[aItems.length - 1];
		oLastSpan.className = oLastSpan.className.replace(/\s*last/, 'position_holder');
	}

	// Add the button.
	var oButtonStripList = oButtonStrip.getElementsByTagName('ul')[0],
		oNewButton = document.createElement('li'),
		oRole = document.createAttribute('role');

	oRole.value = 'menuitem';
	oNewButton.setAttributeNode(oRole);

	if ('sId' in oOptions)
		oNewButton.id = oOptions.sId;
	oNewButton.innerHTML = '<a class="linklevel1" href="' + oOptions.sUrl + '" ' + ('sCustom' in oOptions ? oOptions.sCustom : '') + '><span class="last"' + ('sId' in oOptions ? ' id="' + oOptions.sId + '_text"': '') + '>' + oOptions.sText + '</span></a>';

	oButtonStripList.appendChild(oNewButton);
}

