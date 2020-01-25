/**
 * VueX Store for course editor
 *
 * @author ThimPress
 * @package CourseEditor/JS
 * @version 3.0.0
 */
;(function ($) {

    /**
     * I18n Store
     *
     * @since 3.0.0
     */

    // var LP_Curriculum_i18n_Store = (function (Vue, helpers, data) {
    //     var state = helpers.cloneObject(data.i18n);
    //
    //     var getters = {
    //         all: function (state) {
    //             return state;
    //         }
    //     };
    //
    //     return {
    //         namespaced: true,
    //         state: state,
    //         getters: getters
    //     };
    //
    // })(Vue, LP_Helpers, {});


    /**
     * Sections Store.
     *
     * @since 3.0.0
     */
    var Store = function (Data_Store, create) {
        Store.State = JSON.parse(JSON.stringify(Data_Store));
        var store = {
            namespaced: true,
            state: Store.State,
            getters: Store.Getters,
            mutations: Store.Mutations,
            actions: Store.Actions
        };

        return create ? new Vuex.Store(store) : store;
    };

    Store.Getters = {
        sectionId: function () {
            return Math.random();
        }
    };

    Store.Mutations = {
        updateItemsOrder: function (state, data) {
            state.section.items = data.items;
        },
        newRequest: function () {
        },
        requestComplete: function () {
        }
    };

    Store.Actions = {
        updateItemsOrder: function (context, data) {
        },
        newRequest: function () {
        },
        requestComplete: function () {
        }
    };

    $(document).ready(function () {
        window.FE_Section_Store = Store;
    })

})(jQuery);