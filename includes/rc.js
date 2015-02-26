/*	JAVASCRIPT AND JQUERY FOR ALL PAGES	*/

function toTitleCase(str) {
  return str.replace(/\b\w+\b/g, function titleCaseIt(txt) {
  	return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
  });
}

function getCitySTFromZip(zip) {	//	auto-fill city & state from zipcode
	$.ajax({
		url: 'http://ziptasticapi.com/' + zip,
		statusCode: {
			200: function getCitySTFromZipSuccess(json) {
				var loc = JSON.parse(json);
				$('input[name="city"]').val(toTitleCase(loc.city));
				$('select[name="state"]').val(loc.state);
			}
		}
	});
	return false;
}

function validateForm() {
	var badField = false;	//	assume no bad fields

	//	check if required field exists; if so, check if completed
	if ($("form #name").length && $("form #name").val().length < 2) { badField = "Name field";
	} else if ($("form #email").length && $("form #email").val().length < 7) { badField = "Email address";
	} else if ($("form #phone").length && $("form #phone").val().length < 7) { badField = "Phone number";
	} else if ( ($("form #address").length && $("form #address").val().length < 4) || ($("form #city").length && $("form #city").val().length < 2) ) { badField = "Full Address";
	} else if ($("form #version").length && !$("form input[name='version']:checked").length) { badField = "Version";
	}

	if (badField) {	alert(badField + " is required."); return false;	//	if any bad fields, alert user (and don't submit)
	} else { return true;	//	otherwise submit
	}
}

function rotateImages(numFrames) {
	$("#showpics .photos td").each(function() {
		if ($(this).data("countVal") == null) {
			$(this).data("countVal", numFrames - 1);	//	assign a number to countVal if it doesn't exist
		}

		//$(this).data("countVal", numFrames - 1);	//	assign a number to countVal if it doesn't exist
		var tdCountVal = $(this).data("countVal");	//	assign data to a variable
		var showPicNumber = $(this).attr("data-showpic");	//	assign class to a variable
		$(this).data("countVal",++tdCountVal);	//	change title attribute (increment by 1)

		if (tdCountVal % numFrames == showPicNumber) {	//	use modulo to set this change to only happen every 4 seconds (and different for each pic)
			var oCurPhoto = $(this).children("div.current");
			var oNxtPhoto = oCurPhoto.next();
			if (oNxtPhoto.length == 0) {
				oNxtPhoto = $(this).children("div:first");
			}

			oCurPhoto.removeClass("current").addClass("previous");
			oNxtPhoto.css({ opacity:0.0 }).addClass("current").animate({ opacity: 1.0 }, 500, function() {
				oCurPhoto.removeClass("previous");
			});
		}
	});
}

function getPath(theImage) {
	var endPos = theImage.src.lastIndexOf("/") + 1;	//	find the position of the last "/" mark
	var path = theImage.src.substring(0, endPos);	//	the extension of the image
	return path;
}

function getBasicName(theImage) {
	var endPos = theImage.src.lastIndexOf(".");	//	find ending position of the basic name of the image (e.g. before ".png")
	var startPos = theImage.src.lastIndexOf("/") + 1;	//	find start position of the basic name of the image (e.g. after "images/")
	var basicName = theImage.src.substring(startPos, endPos);	//	the basic name of the image
	return basicName;
}

function getExt(theImage) {
	var startPos = theImage.src.lastIndexOf(".") + 1;	//	find the starting position of the extension (after last ".")
	var extension = theImage.src.substring(startPos);	//	the extension of the image
	return extension;
}

//	Initialize JQuery on window load
$(function() {
	//	JQuery Validate Forms
	if ($("form#orderform").length) {	//	if order form exists
		$("form#orderform").submit(function() { return validateForm(); });	//	validate form upon submission


		//	JQuery checkmark on coupon code!
		$("form#orderform #coupon").keyup(function() {
			$("form#orderform #coupon").val($("form#orderform #coupon").val().toUpperCase());	//	convert value to uppercase
			$("form#orderform #redx").css("display","none");	//	make this disappear (temporarily) if it's visible
			$("form#orderform #checkmark").css("display","none");	//	make this disappear (temporarily) if it's visible

			if ($("form#orderform #coupon").val() == "SCHOOL" || $("form#orderform #coupon").val() == "DOLLAR") {	//	if value is SCHOOL, show the green checkmark (accepted coupon code)
				$("form#orderform #checkmark").css("display","inline");
			} else if ($("form#orderform #coupon").val().length) {	//	if value is filled (but is not SCHOOL), show red x
				$("form#orderform #redx").css("display","inline");
			}
		});
	}
	if ($("form#licenseform").length) {	//	if licensing form exists
		$("form#licenseform").submit(function() { return validateForm(); });	//	validate form upon submission
	}

	// JQuery Image Rotator!
	if ($("#showpics").length) {	//	if this div is there, rotate images
		var numFrames = 6;	//	total number of frames on page (in which to show pics)
		setInterval("rotateImages(" + numFrames + ")", 800);
	}

	//	JQuery Rollover!
	$("img.mouseover").each(function() {	//	activate this on images with the class "mouseover"
		var thisImage = $(this);
		var oldSrc = thisImage.attr("src");	//	non-mouseover src
		var overSrc = getPath(this) + getBasicName(this) + "_over." + getExt(this);	// (e.g. "images/myimage_over.png")

		thisImage.hover(function() {
			thisImage.attr("src", overSrc);	//	change to overSrc when mousing over
		}, function() {
			thisImage.attr("src", oldSrc);	// change back to oldSrc when mouse leaves
		});
	});

	//	JQuery Hide stuff in license form!
	if ($("form#licenseform").length) {
		if ($("form #version_full:checked").length < 1) {
			$("form tr.full_only").addClass("hidden");
		}

		$("form #version input").click(function() {
			if ($("form #version_full:checked").length) {
				$("form tr.full_only").removeClass("hidden");
			} else {
				$("form tr.full_only").addClass("hidden");
			}
		});
	}

	// JQuery Rollover Slide Enlarge!
	$("img.enlarge").each(function() {	//	activate this on images with the class "enlarge"
		var thisImage = $(this);	//	regular size image
		var bigId = getBasicName(this) + "_large"	//	id of new image is basename of image + "_large"
		var bigSrc = getPath(this) + bigId + "." + getExt(this);	//	src of big image is similare to regular
		thisImage.parent().append("<div class='overlarge' id='" + bigId + "'><img src='" + bigSrc + "' alt=''></div>");	//	create a new div with the image in it and append it as a sibling
		var bigImage = $("#"+bigId);	//	we are creating a jQuery reference to the div element even though we don't know its id

		thisImage.hover(function() {
			var offset = thisImage.offset();	//	get the offset (location on screen) of the current image

			bigImage.css("top",offset.top + thisImage.height() + 10).css("left",offset.left).slideDown(300);	//	set position and slide big image down
			thisImage.fadeTo(300, 0.3);	//	fade small image down to 30% opacity
		}, function() {	//	on mouseout
			bigImage.slideUp(200, function() {
				bigImage.css("display","none");	//	make sure it's gone
				bigImage.stop(true);	//	stop animations (clear the queue = true)
			});

			thisImage.fadeTo(200, 1.0, function() {	//	fade small image back up to 100% opacity
				thisImage.stop(true);	//	stop animations (clear the queue = true)
			});
		});
	});

	//	JQuery AJAX get city & state from zip!
	$(document).on('keyup', 'input#zip', function() {
		var zip = $(this).val();

		if(zip.length === 5) {
			getCitySTFromZip(zip);
		}
	});

	//	JQuery UI Tooltips!
	$(document).tooltip();	//	everything with title attribute will have a tooltip
	$('.tooltip-html').tooltip({
    content: function() {
      return $(this).attr('title');
    }
	});
});

function replaceShowPics() {	//	AJAX call to change photos
	$.ajax({
		url: 'productions.php',	//	also works as http://rhythmcity.org/productions.php
		statusCode: {
			200: function replaceShowPicsSuccess(pageData) {
				$("table#showpics").replaceWith($(pageData).find("table#showpics"));
			}
		}
	});
	return false;
}

	//	Create a link to a pre-filled licensing quote form
function createPrefilledLink() {
	var form = $("#licenseform");
	var queryString = (form.find("input[name='name']").val().length ? '&name=' + form.find("input[name='name']").val() : '') +
		(form.find("input[name='company']").val().length ? '&company=' + form.find("input[name='company']").val() : '') +
		(form.find("input[name='email']").val().length ? '&email=' + form.find("input[name='email']").val() : '') +
		(form.find("input[name='phone']").val().length ? '&phone=' + form.find("input[name='phone']").val() : '') +
		(form.find("input[name='address']").val().length ? '&address=' + form.find("input[name='address']").val() : '') +
		(form.find("input[name='city']").val().length ? '&city=' + form.find("input[name='city']").val() : '') +
		(form.find("select[name='state']").val() !== "--" ? '&state=' + form.find("select[name='state']").val() : '') +
		(form.find("input[name='zip']").val().length ? '&zip=' + form.find("input[name='zip']").val() : '') +
		(form.find("input[name='website']").val().length ? '&website=' + form.find("input[name='website']").val() : '') +
		(form.find("textarea[name='comments']").val().length ? '&comments=' + form.find("textarea[name='comments']").val() : '');

	return 'http://rhythmcity.org/licensing.php?prefill=true' + queryString.replace(/ /g, "%20");
}
