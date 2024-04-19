jQuery(document).ready( function($) {
    emqa_open_pointer(0);
    function emqa_open_pointer(i) {
        pointer = emqaPointer.pointers[i];
        options = $.extend( pointer.options, {
            close: function() {
                $.post( ajaxurl, {
                    pointer: pointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
                if( emqaPointer.pointers[i+1] ) {
                    emqa_open_pointer(i+1);
                }
            }
        });
 
        $(pointer.target).pointer( options ).pointer('open');
    }
});