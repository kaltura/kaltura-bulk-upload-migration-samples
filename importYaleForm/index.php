<!doctype html>
<html>
<head>
	<title></title>
	<!-- Styles -->
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="css/ie_fieldset_fix.css" />
	<![endif]-->
	<link rel="stylesheet" href="css/uniform.aristo.css" type="text/css" />
	<link rel="stylesheet" href="css/ui.reformed.css" type="text/css" />
	<link rel="stylesheet" href="css/jquery-ui-1.8.7.custom.css" type="text/css" />
	<!-- Scripts -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	<script src="js/jquery.uniform.min.js" type="text/javascript"></script>
	<script src="js/jquery.validate.min.js" type="text/javascript"></script>
	<script src="js/jquery.ui.reformed.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(function(){ //on doc ready
		//set validation options
		//(this creates range messages from max/min values)
		$.validator.autoCreateRanges = true;
		$.validator.setDefaults({
		    highlight: function(input) {
		        $(input).addClass("ui-state-highlight");
		    },
		    unhighlight: function(input) {
		        $(input).removeClass("ui-state-highlight");
		    },
		    errorClass: 'error_msg',
		    wrapper : 'dd',
		    errorPlacement : function(error, element) {
		        error.addClass('ui-state-error');
		        error.prepend('<span class="ui-icon ui-icon-alert"></span>');
		        error.appendTo(element.closest('dl.ui-helper-clearfix').effect('highlight', {}, 2000));
		    }
		});
		
		//call reformed and the validation library on your form
		$('#import_form').reformed().validate();
		});
	</script>
</head>
<body>
	<div class="reformed-form">
		<h1>Import Course Content from <a href="http://oyc.yale.edu/" target="_blank">Open Yale Courses</a> to Kaltura</h1>
		<p>
			This tool creates a Kaltura Bulk Upload XML file for importing course content from http://oyc.yale.edu/ to Kaltura.<br />
			After downloading the XML created by this tool, use the KMC>Upload>Submit Bulk>Selet CSV/XML>Entries CSV/XML.<br />
			<span style="font-style: italic;"><strong>Note: </strong>After clicking submit once, be patiant for the XML file download to begin. Do not press twice.</span>
		</p>
		<form method="post" name="import_form" id="import_form" action="openyalecourseimport.php">
			<dl>
				<dt>
					<label for="firstCoursePage">First Page of the Course to Import</label>
				</dt>
				<dd><input type="text" id="firstCoursePage" class="required  url" name="firstCoursePage" value="http://oyc.yale.edu/political-science/plsc-270/lecture-6" /></dd>
			</dl>
			<dl>
				<dt>
					<label for="courseTags">Course Tags (To be set in Kaltura)</label>
				</dt>
				<dd><input type="text" id="courseTags" name="courseTags" value="education,finance,economics,yale" /></dd>
			</dl>
			<dl>
				<dt>
					<label style="margin-right: 120px;" for="categoryKaltura">Category in Kaltura</label>
				</dt>
				<dd><input type="text" id="categoryKaltura" class="required" name="categoryKaltura" value="education>finance>financial theory" /></dd>
			</dl>
			<div id="submit_buttons">
				<button type="reset">Reset</button>
				<button type="submit">Submit</button>
			</div>
			</form>
	</div>
	<p style="color:#ccc;font-size: small;font-style: italic;">* This form was created using <a href="http://www.reformedapp.com/">reformed</a></p>
</body>
</html>
