define(['jquery', 'core/templates', 'core/modal_factory', 'auth_mumie/mumie_server_config'],
    function($) {
        return {
            init: function(contextid) {
                setAddServerListeners(contextid);
                setEditButtonListeners(contextid);
            }
        };

        /**
         * Set click listener to add server button
         * @param {number} contextid
         */
        function setAddServerListeners(contextid) {
            require(['auth_mumie/mumie_server_config'], function(MumieServer) {
                MumieServer.init($('#mumie_add_server_button'), contextid);
            });
        }

        /**
         * Set click listeners for the edit buttons
         * @param {number} contextid
         */
        function setEditButtonListeners(contextid) {
            var names = $(".mumie_list_entry_name");
            var urls = $(".mumie_list_entry_url");
            var editBtns = $(".mumie_list_edit_button");
            var ids = $(".mumie_list_entry_id");

            editBtns.each(function(i) {
                var formdata = "id=" + ids[i].textContent +
                    "&_qf__mumieserver_form=1&name=" + names[i].textContent + "&url_prefix=" + urls[i].textContent;
                require(['auth_mumie/mumie_server_config'], function(MumieServer) {
                    MumieServer.init(editBtns[i], contextid, formdata);
                });
            });
        }
    });