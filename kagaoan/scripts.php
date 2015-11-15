<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
?>   
    <!-- STYLES -->
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>font-face.css">
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>style.css">
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>button.css">
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>form.css">
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>jquery.bubblepopup.v2.3.1.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>jquery.countdown.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>jquery.confirm.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>jquery-ui-1.8.20.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>jquery-ui-timepicker-addon.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>dcaccordion.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>animate.css" />
    <link rel="stylesheet" type="text/css" href="<?=__STYLE__?>notification.css" />
    
    <!-- SCRIPTS -->
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery-1.7.2.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery-ui-1.8.20.custom.min.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.metadata.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.mockjax.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.form.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.validate.js"></script>
    <script type='text/javascript' src="<?=__SCRIPT__?>jquery.cookie.js"></script>
    <script type='text/javascript' src="<?=__SCRIPT__?>jquery.hoverIntent.minified.js"></script>
    <script type='text/javascript' src="<?=__SCRIPT__?>jquery.dcjqaccordion.2.7.min.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.bubblepopup.v2.3.1.min.js" ></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.countdown.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.confirm.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.ajaxfileupload.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.PrintArea.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>jquery.notification.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>FancyZoom.js" ></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>FancyZoomHTML.js" ></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>json.js"></script>
    <script type="text/javascript" src="<?=__SCRIPT__?>ajax.js"></script>
	<script type="text/javascript" src="<?=__SCRIPT__?>script.js"></script>
    
    <!--[if gte IE 9]>
	  <style type="text/css">
	    .gradient, .gradient-horizontal-gray {
	       filter: none;
	    }
	  </style>
	<![endif]-->

	<!-- FAVICON -->
	<link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
	<link rel="manifest" href="favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#00FFFFFF">
	<meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#00FFFFFF">

    <script type="text/javascript">
    var wait = 10;
	function doCountdown(){
		var msg = "<?=$MESSAGE?>";
		var type = "<?=$MESSAGETYPE?>";

		$.noti(type, msg);
	};

	function clearForm(e) {
        $(e).find(':input').each(function() {
            switch(this.type) {
                case 'password':
                case 'select-multiple':
                case 'select-one':
                case 'text':
                case 'file':
                case 'textarea':
                    $(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    $(this).attr('checked',false);
                    break;
            }
        });
        
        validator = $(e).validate();
        validator.resetForm();
    }
    
    function countProperties(obj) {
        var prop;
        var propCount = 0;
        
        for (prop in obj) {
            propCount++;
        }
        
        return propCount;
    }
    
    function isNumeric(id){
        var strValidChars = "0123456789.-%";
        var isValid = true;
        var strChar;
        var newValue;
        var strValue = id.val();
        
        // if (strValue.length == 0) return false;
        for (i=0;i<strValue.length && isValid == true; i++){
            strChar = strValue.charAt(i);
            if (strValidChars.indexOf(strChar) == -1) {
                isValid = false;
            }
        }
    
        if (isValid == false){
            newValue = strValue.substring(0, strValue.length-1);
            id.val(newValue);
        }
    }
    
    function isNumber(id){
        var strValidChars = "0123456789";
        var isValid = true;
        var strChar;
        var newValue;
        var strValue = id.val();
        
        // if (strValue.length == 0) return false;
        for (i=0;i<strValue.length && isValid == true; i++){
            strChar = strValue.charAt(i);
            if (strValidChars.indexOf(strChar) == -1) {
                isValid = false;
            }
        }
    
        if (isValid == false){
            newValue = strValue.substring(0, strValue.length-1);
            id.val(newValue);
        }
    }
    
    function setMaxLength(Object, MaxLen){
        if (Object.value.length > MaxLen) {
            Object.value = Object.value.substring(0, MaxLen);
        } 
        // return (Object.value.length <= MaxLen);
    }
    
    function loadImage(){
    	$('#trloadImage').removeClass("hidden");
        $("#loadedImage").addClass("hidden");
        $("#loadImage").removeClass("hidden");
    }
    
    function loadedImage(){
        $("#loadedImage").removeClass("hidden");
        $("#loadImage").addClass("hidden");
    }
    
    function fileLoaded(){
        $("#loadpage").addClass("hidden");
        $("#contents").removeClass("hidden");
    }
	
	function ajaxLoading(){
        $("#loadajax").removeClass("hidden");
        $("#ajax").addClass("hidden");
    }
	
	function ajaxLoaded(){
        $("#loadajax").addClass("hidden");
        $("#ajax").removeClass("hidden");
    }
    
	function ajaxLoad(uri,method){
		ajaxLoading();

		var requestURI = uri;
		var requestMethod = method;
		
		var request = $.ajax({
		  url: requestURI,
		  type: requestMethod,
		  dataType: "html"
		});
		
		request.success(function(content) {
		  	ajaxLoaded();	
			$("#ajax").html(content);
		});
		
		request.fail(function(jqXHR, textStatus) {
		  	ajaxLoaded();
			$("#ajax").html("<div class='spacer_100 clean'><!-- SPACER --></div>Fail loading content.");
		});
	}

	jQuery.validator.addMethod("notEqual", function(value, element, param) {
	  	return this.optional(element) || value != param;
	});

	jQuery.validator.addMethod("requiredIf", function(value, element, param) {
		return parseInt(value) > 0 && parseInt(param.required) == parseInt(param.element.val());
	});

	jQuery.validator.addMethod("requiredIfNotEqual", function(value, element, param) {
		return value.length > 0 && parseInt(param.notequal) != parseInt(param.element.val());
	});

	jQuery.validator.addMethod("requiredIfGreater", function(value, element, param) {
		return parseInt(value) > 0 && parseInt(param.element.val()) > parseInt(param.greaterthan);
	});

	jQuery.validator.addMethod("requiredIfLessThan", function(value, element, param) {
		return parseInt(value) > 0 && parseInt(value) <= parseInt(param.element.html());
	});
	
	jQuery.validator.addMethod("lessThan", function(value, element, param) {
	  	return parseInt(param.val()) > parseInt(value) || parseInt(param.val()) == parseInt(value);
	});
	
	jQuery.validator.addMethod("isNumeric", function(value, element) {
		var strValidChars = "0123456789.-%";
        var isValid = true;
        var strChar;
        var newValue;
        var strValue = value;
        
        for (i=0;i<strValue.length && isValid == true; i++){
            strChar = strValue.charAt(i);
            if (strValidChars.indexOf(strChar) == -1) {
                isValid = false;
            }
        }
        
	  	return (isValid);
	});

	jQuery.validator.addMethod("isNumbers", function(value, element) {
	  	return (parseInt(value));
	});
	
	jQuery.validator.addMethod("ckeditor", function(value, element) { 
	    var textData = editor.getData();
	    if(textData.length>0) return true;
	    return false;
	});
	
    $(document).ready(function() {		
		<?=$MESSAGE != ""?"doCountdown();":"";?>

		$(".datepicker").datetimepicker({
    		dateFormat: "yy-mm-dd",
    		showSecond: false,
    		timeFormat: 'hh:mm:ss'
    	});

    	$('.isNumeric').keyup(function() {
        	var _this = $(this);
    		isNumeric(this);
    	});
    	
    	var content_height = $(window).height() - 285;
    	var content_width = $(window).width() - 350;
    	$('.main-width').css('width', content_width+'px');
    	$('#main-height').css('min-height', content_height+'px');
    	
        $('#accordion').dcAccordion({
            eventType: 'click',
            autoClose: true,
            saveState: false,
            disableLink: true,
            speed: 'slow',
            showCount: false,
            autoExpand: true,
            cookie	: 'dcjq-accordion',
            classExpand	 : 'dcjq-current-parent'
        });
        
        $('input[type="reset"]').click(function(){
            clearForm(this.form);
        });
        
        $('.dummy_trigger').click(function(){
            $(this).prev().click();
            $(this).prev().focus();
        });

        $('.isNumeric').bind('keypress keyup', function(e) {
        });     
        
        $('.trigger').change(function(){
            $(this).next().css("text-align","left");
            $(this).next().val($(this).val());
            $(this).blur();
            $(this).next().focus();
        });
		
		jQuery('.disablePaste').keydown(function(event) {
			var forbiddenKeys = new Array('c', 'x', 'v');
			var keyCode = (event.keyCode) ? event.keyCode : event.which;
			var isCtrl;
			isCtrl = event.ctrlKey
			if (isCtrl) {
				for (i = 0; i < forbiddenKeys.length; i++) {
					if (forbiddenKeys[i] == String.fromCharCode(keyCode).toLowerCase()) {
						 return false;
					}
				}
			}
			return true;
		});
    });
    </script>
<? 
endif;
?>