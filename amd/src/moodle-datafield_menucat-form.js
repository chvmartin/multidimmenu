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
	            var contentid= this.getAttribute('contentid');
                var s = document.getElementById('id_second_level');

                if (this.value > 0) {
                    $('#id_second_level').attr('disabled', false)
                } else {
                    $('#id_second_level').attr('disabled', true)
	                secondlevel.empty()
                    s[0] = new Option('Any',0);
                    return
                }
	            console.log('firstlevel '+this.value + 'contendid '+contentid);
                Ajax.call([{
                    methodname: 'datafield_menucat_get_second_level',
                    args: {firstlevel: this.value,contentid: contentid},
                    done: function (resp) {
	                    console.log('JSON.stringify(resp)');
	                    console.log(JSON.stringify(resp));
	                    secondlevel.empty()
                        s[0] = new Option('Any',0);
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

		        if (this.value > 0 && this.value > 0) {
			        $('#id_third_level').attr('disabled', false)
		        } else {
			        $('#id_third_level').attr('disabled', true)
			        thirdlevel.empty()
			        s[0] = new Option('Any',0);
			        return
		        }
		        console.log('firstlevel '+firstlevel+' secondlevel '+this.value + 'contendid '+contentid);
		        Ajax.call([{
			        methodname: 'datafield_menucat_get_third_level',
			        args: {firstlevel:firstlevel ,secondlevel: this.value,contentid: contentid},
			        done: function (resp) {
				        console.log('third(resp)');
				        console.log(JSON.stringify(resp));
				        thirdlevel.empty()
				        s[0] = new Option('Any',0);
				        resp.forEach(function(element) {
					        s[s.options.length] = new Option(element.thirdlevelitem,element.id);
				        });
			        }
		        }]);
	        });
        };

        return {
            init: init
        };
    });
