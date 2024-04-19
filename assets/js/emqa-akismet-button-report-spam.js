jQuery(document).ready(function($){
	$('.emqa_report_spam').on('click', function(){
		if(confirm("Are you sure?")){
			var post_id = $(this).data("post");
			var nonce = $(this).data("nonce");
			var emqa_this = $(this);
			// console.log('abc');
			$.ajax({
				url: emqa.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'emqa_report_spam_to_admin',
					nonce: nonce,
					post_id: post_id,
				},
				success: function( data ) {
					console.log( data );
					if (data.success) {
						alert(data.data.message);
						emqa_this.remove();
					}else{
						alert(data.data.message);
					}
				}
			});
		}
	});

});