$(function(){
		$('.ptogtitle').click(function(){
			if($(this).hasClass('vsble'))
			{
				$(this).removeClass('vsble');
				$('#main-table-box #crudForm').slideDown("slow");
			}
			else
			{
				$(this).addClass('vsble');
				$('#main-table-box #crudForm').slideUp("slow");
			}
		});

		$( '#main-table-box' ).find( '.form-group' ).each( function() {
			$( this ).find( '[name]' ).bind( 'focus', function(){
				$( this ).closest( '.form-group' ).find( '.error-block' ).remove();
				$( this ).closest( '.form-group' ).removeClass( 'has-error' );
				$( this ).closest( '.form-group' ).find( '.help-block' ).show();
			});
		});

		var save_and_close = false;

		$('#save-and-go-back-button').click(function(){
			save_and_close = true;

			$('#crudForm').trigger('submit');
		});

		$('#crudForm').submit(function(){
			var my_crud_form = $(this);
			var form_data 			=	{};
			$( this ).find( '[id^="field-"]' ).each( function(){
				form_data[ $( this ).attr( 'name' ) ] 	=	$( this ).val();
			});

			$.ajax({
				url			: validation_url,
				dataType		: 'json',
				type 		:	'POST',
				data 		: form_data,
				beforeSend	: function(){
					$("#FormLoading").show();
					$( '#main-table-box' ).find( '.form-group' ).each( function() {
						$( this ).find( '.error-block' ).remove();
						$( this ).removeClass( 'has-error' );
						$( this ).find( '.help-block' ).show();
					});
				},
				success: function(data){
					$("#FormLoading").hide();
					if(data.success)
					{
						$.ajax({
							dataType: 'json',
							url 	:	$( my_crud_form ).attr( 'action' ),
							cache: 'false',
							data 	:	form_data,
							type 	:	'POST',
							beforeSend: function(){
								$("#FormLoading").show();
							},
							success: function(data){
								$("#FormLoading").fadeOut("slow");
								// data = $.parseJSON( result );
								if(data.success)
								{
									var data_unique_hash = my_crud_form.closest(".flexigrid").attr("data-unique-hash");

									$('.flexigrid[data-unique-hash='+data_unique_hash+']').find('.ajax_refresh_and_loading').trigger('click');

									if(save_and_close)
									{
										if ($('#save-and-go-back-button').closest('.ui-dialog').length === 0) {
											window.location = data.success_list_url;
										} else {
											$(".ui-dialog-content").dialog("close");
											success_message(data.success_message);
										}

										return true;
									}

									$('.field_error').each(function(){
										$(this).removeClass('field_error');
									});
									clearForm();
									form_success_message(data.success_message);
								}
								else
								{
									// console.log( message_insert_error );
									// alert( message_insert_error );
									$.each(data.error_fields, function(index,value){
										$( '#field-' + index ).closest( '.form-group' ).addClass( 'has-error' );
										$( '#field-' + index ).closest( '.form-group' ).find( '.help-block' ).hide();
										$( '#field-' + index ).after( '<p class="help-block error-block">' + value + '<p>' );
										// $('input[name='+index+']').addClass('field_error');
									});
								}
							},
							error: function( data ){
								// alert( message_insert_error );
								$.each(data.error_fields, function(index,value){
									$( '#field-' + index ).closest( '.form-group' ).addClass( 'has-error' );
									$( '#field-' + index ).closest( '.form-group' ).find( '.help-block' ).hide();
									$( '#field-' + index ).after( '<p class="help-block error-block">' + value + '<p>' );
									// $('input[name='+index+']').addClass('field_error');
								});
								$("#FormLoading").hide();
							}
						});
					}
					else
					{
						$('.field_error').removeClass('field_error');
						// form_error_message(data.error_message);
						$.each(data.error_fields, function(index,value){
							$( '#field-' + index ).closest( '.form-group' ).addClass( 'has-error' );
							$( '#field-' + index ).closest( '.form-group' ).find( '.help-block' ).hide();
							$( '#field-' + index ).after( '<p class="help-block error-block">' + value + '<p>' );
							// $('input[name='+index+']').addClass('field_error');
						});
						error_message( 'An error has occured during the operation.' );
					}
				},
				error: function(){
					error_message (message_insert_error);
					$("#FormLoading").hide();
				}
			});
			return false;
		});

		if( $('#cancel-button').closest('.ui-dialog').length === 0 ) {

			$('#cancel-button').click(function(){
				if( confirm( message_alert_add_form ) )
				{
					window.location = list_url;
				}

				return false;
			});

		}
	});

	function clearForm()
	{
		$('#crudForm').find(':input').each(function() {
	        switch(this.type) {
	            case 'password':
	            case 'select-multiple':
	            case 'select-one':
	            case 'text':
	            case 'textarea':
	                $(this).val('');
	                break;
	            case 'checkbox':
	            case 'radio':
	                this.checked = false;
	        }
	    });

		/* Clear upload inputs  */
		$('.open-file,.gc-file-upload,.hidden-upload-input').each(function(){
			$(this).val('');
		});

		$('.upload-success-url').hide();
		$('.fileinput-button').fadeIn("normal");
		/* -------------------- */

		$('.remove-all').each(function(){
			$(this).trigger('click');
		});

		$('.chosen-multiple-select, .chosen-select, .ajax-chosen-select').each(function(){
			$(this).trigger("liszt:updated");
		});
	}