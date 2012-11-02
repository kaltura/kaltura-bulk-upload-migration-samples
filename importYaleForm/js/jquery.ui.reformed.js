/**
 * @package: Reformed Form Generator
 * @url: http://www.reformedapp.com
 * 
 * jQuery UI Plugin
 * jquery.ui.reformed.js
 * Adds appropriate classes to form elements so they may be styled by jQuery UI
 * Adds Uniform.js styling to file inputs, selects, radios, and checkboxes
 *
 * @author: Paul Pullen
 * @copyright: Copyright 2010 Paul Pullen
 * @email: paulpullen@gmail.com
 *
 * @version: 1.0
 */

(function($){

    $.widget("ui.reformed", {

        //default options
        options: {
            styleFileInputs : true, //use the uniform plugin to style file input boxes
            styleRadios : true, //style radios with uniform plugin
            styleCheckboxes : true, //style checkboxes with uniform plugin
            styleSelects : true, //style selects with uniform plugin
            styleButtonsWithUniform : false, //style all form buttons with uniform (false = styled by jquery UI)
            styleDatepicker : true //use jqueryUI datepicker
        },

        //init
        _init: function() {

            var self = this; //the plugin object
            var o = self.options; //user options

            //get all form elements
            var form = self.element; //the form that reformed was called on
            var fieldsets = form.find('fieldset');
            var legends = form.find('legend');
            var text_inputs = form.find('input[type="text"] , input[type="password"] , textarea');
            var selects = form.find('select');
            var file_inputs = form.find('input[type="file"]');
            var checkboxes = form.find('input[type="checkbox"]');
            var radios = form.find('input[type="radio"]');
            var buttons = form.find('input[type="reset"] , input[type="submit"], button');
            var datepickers = form.find('input[type="text"].datepicker');
            var human_inputs = form.find('input[type="text"].human');

            //add appropraite styles to form elements
            form.addClass('ui-widget');
            fieldsets.addClass('ui-widget ui-widget-content ui-corner-all');
            legends.addClass('ui-widget ui-widget-header ui-corner-all');
            text_inputs.addClass('ui-widget ui-widget-content ui-corner-all');
                        
            //add ui-helper-clearfix to dls
            form.find('dl, fieldset').addClass('ui-helper-clearfix');

            //add highlight states on hover for text-based inputs
            text_inputs.each(function(){
                $(this).focusin(function() {
                    $(this).addClass('ui-state-highlight');
                });
                $(this).focusout(function() {
                    $(this).removeClass('ui-state-highlight');
                });
            });
            
            //add button styles
            buttons.each(function() {
                //already styled? skip it to avoid restyling issues
                //check if already has class of ui-button
                if( $(this).hasClass('ui-button') === false ) {
                    $(this).button();
                }
            });

            //style file, select, radio, and checkbox inputs with uniform plugin
            if(o.styleFileInputs) {
                self._styleWithUniform(file_inputs);
            }
            if(o.styleSelects) {
                self._styleWithUniform(selects);
            }
            if(o.styleRadios) {
                self._styleWithUniform(radios);
            }
            if(o.styleCheckboxes) {
                self._styleWithUniform(checkboxes);
            }
            if(o.styleButtonsWithUniform) {
                self._styleWithUniform(buttons);
            }

            //add jQuery UI datepicker to inputs with class of "datepicker"
            if(o.styleDatepicker) {
                datepickers.each(function(){
                    //need to apply scoped CSS class directly to #ui-datepicker-div and all descendants to pick up the reformed-form class
                    //http://forum.jquery.com/topic/using-datepicker-with-different-ui-theme-than-the-rest-of-the-site
                    //need to remove hasDatepicker class if already exists or reapplication of datepicker will not work.
                    $(this).removeClass('hasDatepicker').datepicker();
                    //only wrap if not already wrapped
                    if($('#ui-datepicker-div').data('wrapped') !== true){
                        $('#ui-datepicker-div').wrap('<div class="reformed-form"></div>').data('wrapped', true);
                    }
                });
            }

            //hook up simple human verification if present
            human_inputs.each(function(){

                //if question already been added, use this one again (check for data.added = true)
                if ( $(this).data('added') !== true){
                    var input = $(this);
                    //remember that it's been "questionized" -- (this prevents some errors when changing themes with SHV)
                    input.data('added', true);
                    
                    var q_label = $(this).parent().parent().find('label.verification_question');
                    var submit_button = form.find('input[type="submit"], button[type="submit"]');
                    //disable submit button
                    submit_button.attr('disabled',true);

                    //setup questions array
                    var qas = [
                    "What is 1 + 7?;8",
                    "What is 2 + 5?;7",
                    "What is 3 - 2?;1",
                    "What is 10 - 7?;3",
                    "What is 5 - 0?;5"
                    ];

                    //choose random question/answer from array
                    var random_qa = qas[Math.floor(Math.random() * qas.length)];
                    //split question from answer
                    var qa = random_qa.split(';');
                    var question = qa[0];
                    var answer = qa[1];

                    //find label and insert question
                    q_label.html("Human Verification: " + question);

                    //on lose focus, check answer
                    //if correct, allow form submit
                    input.keyup(function(){
                        //remove existing error div
                        $('dd#verification_error').remove();

                        //get response
                        var response = $(this).val();
                        //alert(response);
                        if(response === answer){
                            //success! allow submit
                            submit_button.attr('disabled', false);
                            //remove error div
                            $('dd#verificaton_error').remove();
                        } else {
                            //oops...wrong answer
                            //focus on field
                            $(this).val('').focus();
                            $(this).parent().append('<dd id="verification_error" class="ui-state-error" style="display: block;">\n\
                                                <span class="ui-icon ui-icon-alert"></span>\n\
                                                <label class="error_msg" style="display: block;">Incorrect verification answer. Please try again.</label></dd>');
                        }
                    });//end keyup function
                } // end if
            });//end human_inputs each


        }, //end init

        //style selects, radios, checkboxes, and file inputs with uniform plugin
        //http://pixelmatrixdesign.com/uniform/
        _styleWithUniform: function(selector) {
            var s = selector;
            
            //check to see if these elements have already had uniform applied
            //if so, do not apply again
            //then, update to account for dynamically added items
            
            $.each(s, function() {
                //if multiple select, add ui-widget-content and left-rounded corners classes instead of uniform
                if( $(this).is('select') && ($(this).attr('multiple') == "multiple" || $(this).attr("size") > 1 ) ) {
                    $(this).addClass('ui-widget-content ui-corner-tl ui-corner-bl');
                } else {
                    //already styled? skip it (if uniformed isn't true, style element)
                    if($(this).data('uniformed') !== true) {
                        //add style and mark input as styled with data attribute
                        //use HTML5 data-xxxx="xxx" instead of .data() so it will be retained when editing xhtml
                        $(this).uniform();
                        $(this).attr('data-uniformed', true);
                        $.uniform.update($(this));
                    }
                }
            });

        }, //end styleWithUniform

        //set options after init
        _setOption : function(option,value) {
            //defaults:
            //            styleFileInputs : true, //use the uniform plugin to style file input boxes
            //            styleRadios : true, //style radios with uniform plugin
            //            styleCheckboxes : true, //style checkboxes with uniform plugin
            //            styleSelects : true, //style selects with uniform plugin
            //            styleButtonsWithUniform : false, //style all form buttons with uniform (false = styled by jquery UI)
            //            styleDatepicker : true //use jqueryUI datepicker

            //get option and value
            var o = option;
            var v = value;

            if (o === 'styleFileInputs' && v === false ) {
                //un-uniform file inputs
                var file_inputs = this.element.find('input[type="file"]').removeAttr('data-uniformed');
                $.uniform.restore(file_inputs);

            } else if (o === 'styleRadios' && v === false ) {

                var radios = this.element.find('input[type="radio"]').removeAttr('data-uniformed');
                $.uniform.restore(radios);

            } else if (o === 'styleCheckboxes' && v === false ) {

                var checkboxes = this.element.find('input[type="checkbox"]').removeAttr('data-uniformed');
                $.uniform.restore(checkboxes);

            } else if (o === 'styleSelects' && v === false ) {

                var selects = this.element.find('select').removeAttr('data-uniformed');
                $.uniform.restore(selects);

            } else if (o === 'styleButtonsWithUniform' && v === true ) {

                var buttons = this.element.find('input[type="reset"], input[type="submit"], button').attr('data-uniformed', true);
                buttons.uniform();

            } else if (o === 'styleDatepicker' && v === false ) {

                this.element.find('.hasDatepicker').removeClass('datepicker hasDatepicker');
            }

            $.Widget.prototype._setOption.apply( this, arguments );
        }//end set options

    });//end plugin

})(jQuery);