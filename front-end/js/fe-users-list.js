/**
 * The JS functionality of the plugin.
 *
 * @since 1.0.0
 *
 * @package front-end-users-list
 * @subpackage front-end-users-list/js
 */

jQuery.noConflict();

jQuery(document).ready(function () {

    var users_current_role = '';
    var users_current_orderby = '';
    var users_current_order = '';
    var users_current_page = 1;

    jQuery(function () {

        // Makes request after role navigation filter.
        jQuery('a[data-filter-role]').click(function () {

            jQuery('.users-roles-nav a').removeClass('current');
            jQuery(this).addClass('current');

            load_front_end_users_list_ajax(jQuery(this).attr('data-filter-role'), users_current_orderby, users_current_order, 1);
            return false;
        });

        // Changes page number.
        jQuery('.tablenav-pages .next-page').on('click', function () {
            if (jQuery(this).attr('disabled')) {
                return false;
            }
            users_current_page += 1;
        });

        // Changes page number.
        jQuery('.tablenav-pages .prev-page').on('click', function () {
            if (jQuery(this).attr('disabled')) {
                return false;
            }
            users_current_page -= 1;
        });

        // Makes request after paginator navigation.
        jQuery('.tablenav-pages, .tablenav-pages .prev-page, .tablenav-pages .next-page, .tablenav-pages').on('click', function () {

            if (jQuery(this).attr('disabled')) {
                return false;
            }

            jQuery('.tablenav-pages select').val(users_current_page);
            load_front_end_users_list_ajax(users_current_role, users_current_orderby, users_current_order, users_current_page);
            return false;
        });

        // Makes request after page selector changed.
        jQuery('.tablenav-pages select').on('change', function () {

            jQuery('.tablenav-pages select').val(jQuery(this).val());
            load_front_end_users_list_ajax(users_current_role, users_current_orderby, users_current_order, jQuery(this).val());
        });

        // Makes request after role navigation order.
        jQuery('a[data-sort-orderby]').click(function () {

            jQuery(this).blur();

            // If order by field changes to another field, then we should set default order type.
            if (jQuery(this).parent('th').hasClass('sortable')) {

                jQuery('.users-list-table th.sorted')
                    .removeClass('sorted asc')
                    .addClass('sortable desc');

                jQuery(this).attr('data-sort-order', 'asc');
            }

            load_front_end_users_list_ajax(users_current_role, jQuery(this).attr('data-sort-orderby'), jQuery(this).attr('data-sort-order'), users_current_page);

            // Set new order type for the next request.
            var new_order_dir = jQuery(this).attr('data-sort-order') === 'asc' ? 'desc' : 'asc';
            jQuery(this).attr('data-sort-order', new_order_dir);

            jQuery(this).parent('th')
                .removeClass('sortable')
                .addClass('sorted')
                .toggleClass('asc desc');

            return false;
        });

        /**
         * Makes an ajax request.
         */
        function load_front_end_users_list_ajax(role, orderby, order, paged) {

            var postData = {
                'action': 'load_front_end_users_list',
                'role': role,
                'orderby': orderby,
                'order': order,
                'paged': paged,
                'nonce': front_end_users_list.nonce
            };

            // Set passed arguments as current query options
            users_current_role = role;
            users_current_orderby = orderby;
            users_current_order = order;
            users_current_page = parseInt(paged);

            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: front_end_users_list.ajaxurl,
                data: postData,
                success: function (res) {

                    var container = jQuery('#list_table_body');
                    container.html('');
                    if (!res || !res.all_user_elements || !res.user_elements.length) {
                        var row = jQuery(jQuery('#user_table_noresults').html());
                        container.append(row);
                        return;
                    }

                    users_current_pages = res.total_pages;

                    // Create table row for each result item
                    jQuery.each(res.user_elements, function (i, item) {
                        var row = jQuery(jQuery('#user_table_row').html());

                        row.find('#user_name_link').text(item.user_name).prop('href', item.user_link);
                        row.find('#display_name').html(item.display_name);
                        row.find('#user_email').text(item.user_email);
                        row.find('#user_roles').html(item.user_roles);
                        container.append(row);
                    })

                    // If total page number changed, regenerate select options
                    if (res.total_pages && jQuery('.tablenav-pages select:first option').length !== res.total_pages) {

                        jQuery('.tablenav-pages select').empty();
                        for (var x = 1; x <= parseInt(res.total_pages); x++) {
                            jQuery('.tablenav-pages select').append(jQuery('<option/>').prop('value', x).text(x));
                        }
                    }

                    // Disable/Enable previous page status depending on current page
                    if (users_current_page <= 1) {
                        jQuery('.tablenav-pages .prev-page').attr('disabled', 'disabled');
                    } else {
                        jQuery('.tablenav-pages .prev-page').removeAttr('disabled');
                    }

                    // Disable/enable next page link depending on current page & total pages
                    if (users_current_page + 1 > res.total_pages) {
                        jQuery('.tablenav-pages .next-page').attr('disabled', 'disabled');
                    } else {
                        jQuery('.tablenav-pages .next-page').removeAttr('disabled');
                    }

                    // Change labels to new total found count
                    jQuery('.tablenav-pages .total-pages').text(res.total_pages_formatted);

                    // Hide paginator if only one page.
                    if (res.total_pages == 1) {
                        jQuery('.tablenav-pages').addClass('one-page');
                    } else {
                        jQuery('.tablenav-pages').removeClass('one-page');
                    }
                },
            });
            return false;
        }
    });
});
