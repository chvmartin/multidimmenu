define(['jquery', 'core/ajax'],
    function($, Ajax, Str, ModalFactory, ModalEvents, Notification, CustomEvents, Templates) {
        /**
         * Init this module which allows activity completion state to be changed via ajax.
         * @method init
         * @param {string} fullName The current user's full name.
         * @private
         */
        const init = function () {
            // Register the click, space and enter events as activators for the trigger element.
            $('#id_first_level').on("change",function(event) {

                var secondlevel = $('#id_second_level');
				var thirdlevel = $('#id_third_level');
	            var contentid= this.getAttribute('contentid');
                var s = document.getElementById('id_second_level');
	            var t = document.getElementById('id_third_level');

				var input = $('#field_'+contentid);
	            console.log(this.value);
                if (this.value) {
                    $('#id_second_level').attr('disabled', false)
	                input.val('');
	                input.val($('#id_first_level').find(":selected").text());
                } else {
                    $('#id_second_level').attr('disabled', true)
	                secondlevel.empty()
                    s[0] = new Option('Choose...',0);
                    return
                }
                Ajax.call([{
                    methodname: 'datafield_menucat_get_second_level',
                    args: {firstlevel: this.value,contentid: contentid},
                    done: function (resp) {
	                    secondlevel.empty();
						if (Object.keys(resp).length>0){
							s[0] = new Option('Choose...',0);
							$('#id_second_level').attr('disabled', false);
						}else{
							s[0] = new Option('--',0);
							$('#id_second_level').attr('disabled', true);
						}
	                    thirdlevel.empty();
	                    t[0] = new Option('--',0);
                        resp.forEach(function(element) {
                            s[s.options.length] = new Option(element.secondlevelitem,element.id);
                        });
                    }
                }]);
            });

	        $('#id_second_level').on("change",function(event) {
		        var firstlevel = $('#id_first_level').find(":selected").text();
		        var thirdlevel = $('#id_third_level');
		        var contentid= this.getAttribute('contentid');
		        var s = document.getElementById('id_third_level');
		        var input = $('#field_'+contentid);

		        if (this.value) {
			        $('#id_third_level').attr('disabled', false)
			        input.val('');
			        input.val($('#id_first_level').find(":selected").text()+'_'+$('#id_second_level').find(":selected").text());
		        } else {
			        $('#id_third_level').attr('disabled', true)
			        thirdlevel.empty()
			        s[0] = new Option('Choose...',0);
			        return
		        }
		        Ajax.call([{
			        methodname: 'datafield_menucat_get_third_level',
			        args: {firstlevel:firstlevel ,secondlevel: this.value,contentid: contentid},
			        done: function (resp) {
				        thirdlevel.empty()
				        if (Object.keys(resp).length>0){
					        s[0] = new Option('Choose...',0);
					        $('#id_third_level').attr('disabled', false);
				        }else{
					        s[0] = new Option('--',0);
					        $('#id_third_level').attr('disabled', true);
				        }
				        resp.forEach(function(element) {
					        s[s.options.length] = new Option(element.thirdlevelitem,element.id);
				        });
			        }
		        }]);
	        });
	        $('#id_third_level').on("change",function(event) {
		        var contentid= this.getAttribute('contentid');
		        var input = $('#field_'+contentid);
		        if (this.value) {
			        $('#id_third_level').attr('disabled', false)
			        input.val('');
			        input.val($('#id_first_level').find(":selected").text()+'_'+$('#id_second_level').find(":selected").text()+'_'+$('#id_third_level').find(":selected").text());
		        }
	        });
        };

        return {
            init: init
        };
    });
