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
            $('#id_level1').on("change",function(event) {

                var courses = $('#id_level2');
	            var contentid= $('#id_level1').attr('contentid');
                var s = document.getElementById('id_level2');

                if (this.value > 0) {
                    $('#id_level2').attr('disabled', false)
                } else {
                    $('#id_level2').attr('disabled', true)
                    courses.empty()
                    s[0] = new Option('Any',0);
                    return
                }

                Ajax.call([{
                    methodname: 'datafield_menucat_get_level2',
                    args: {level1: this.value,contentid: this.contentid},
                    done: function (resp, err) {
                        courses.empty()
                        s[0] = new Option('Any',0);

                        resp.forEach(function(element) {
                            s[s.options.length] = new Option(element.fullname,element.id);
                        });
                    }
                }]);
            });
        };

        return {
            init: init
        };
    });
