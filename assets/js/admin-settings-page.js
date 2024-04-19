jQuery(document).ready(function($) {

    function hide_item(element,element2) {
        if ($(element).is(':checked')) {
            $(element2).attr('disabled','disabled');
            if ($(element2).is(':checked')) {
                $(element2).removeAttr('checked');
            }
        } else {
            $(element2).removeAttr('disabled');
        }
    }

    hide_item('#emqa_options_emqa_show_all_answers','#emqa_setting_answers_per_page');
    hide_item('#emqa_options_emqa_disable_question_status','#emqa_options_enable_show_status_icon');

    $('#emqa_options_emqa_show_all_answers').on('change',function() {
        hide_item(this,'#emqa_setting_answers_per_page');
    });

    $('#emqa_options_emqa_disable_question_status').on('change',function(){
        hide_item(this,'#emqa_options_enable_show_status_icon');
    });

    $('#emqa-message').on('click', function(e){
        document.cookie = "qa-pro-notice=off";
    });
});