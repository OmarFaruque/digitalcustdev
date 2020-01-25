<?php
/**
 * @author  ThimPress
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

$itemsPerPage = array( 5, 10, 20, 50, 100 );
?>
<script type="text/x-template" id="tmpl-e-modal-select-items">
    <div id="e-modal-items" :class="modalClass()">
        <div class="e-settings-window e-modal-window">
            <div class="e-item-settings-head e-modal-head">
                <h3>{{modalTitle || '<?php _e( 'Select items', 'learnpress-frontend-editor' ); ?>'}}</h3>
                <a href="" class="close" @click="_close"></a>
            </div>
            <div class="e-item-settings-content e-modal-content">
                <div v-if="view=='listing'">
                    <h4 class="e-types">
                        <template v-for="(itemType, i) in modalData.postTypes">
                            <a v-if="!isCurrentTab(itemType.type)" @click="_changeType($event, itemType.type)">{{itemType.plural_name
                                || itemType.name}}</a>
                            <span v-if="isCurrentTab(itemType.type)" class="current">{{itemType.plural_name || itemType.name}}</span>
                        </template>
                    </h4>
                    <input type="text" v-model="term" class="e-search-term">
                    <p class="e-filter-items">
                        <label><?php esc_html_e( 'Show', 'learnpress-frontend-editor' ); ?></label>
                        <select v-model="filters.itemsPerPage" @change="queryItems($event)">
							<?php foreach ( $itemsPerPage as $item ) { ?>
                                <option value="<?php echo $item; ?>"><?php echo esc_html( sprintf( __( '%d items', 'learnpress-frontend-editor' ), $item ) ); ?></option>
							<?php } ?>
                        </select>
                        <span><?php esc_html_e( 'items per page', 'learnpress-frontend-editor' ); ?></span>
                        <label><?php esc_html_e( 'Sort by', 'learnpress-frontend-editor' ); ?></label>
                        <span :class="filterClass('name')"
                              data-sort="name"
                              @click="_sort($event, 'name')"><?php esc_html_e( 'name', 'learnpress-frontend-editor' ); ?></span>
                        <span :class="filterClass('date')"
                              data-sort="date"
                              @click="_sort($event, 'date')"><?php esc_html_e( 'date', 'learnpress-frontend-editor' ); ?></span>
                    </p>
                    <div id="e-browse-items">
                        <ul v-if="items.length" class="e-result-items">
                            <li v-for="(item, i) in items" :class="isSelected(item.id) ? 'selected' : ''">
                                <label>
                                    <input type="checkbox" @change="_selectItem" :value="item.id"
                                           :checked="isSelected(item.id)">
                                    {{item.title || '<?php _e( '(No title)', 'learnpress-frontend-editor' ); ?>
                                    '}}</label>
                                <strong class="item-id">(#{{item.id}})</strong>
                            </li>
                        </ul>
                        <p v-if="!items.length"><?php esc_html_e( 'No items', 'learnpress-frontend-editor' ); ?></p>

                    </div>
                </div>
                <div v-if="view=='selected'">
                    <ul v-if="selectedItems.length" class="e-result-items">
                        <li v-for="(item, i) in selectedItems">
                            <label>
                                <input type="checkbox" @change="_selectItem" :value="item.id"
                                       :checked="isSelected(item.id)">
                                {{item.title}}</label>
                            <strong class="item-id">(#{{item.id}})</strong>
                        </li>
                    </ul>
                    <p v-if="!selectedItems.length"><?php esc_html_e( 'No items', 'learnpress-frontend-editor' ); ?></p>
                </div>
            </div>
            <div class="e-item-settings-footer e-modal-footer">
                <button id="e-modal-select" class="e-button e-button-primary" type="button"
                        :disabled="!selectedItems.length"
                        data-label="<?php _e( 'Add Items', 'learnpress-frontend-editor' ); ?>"
                        @click="_select">{{selectButton}}
                    {{countSelectedItems()}}
                </button>
                <button v-show="view=='selected'" class="e-button" type="button"
                        @click="_switch($event, 'listing')"><?php esc_html_e( 'Back', 'learnpress-frontend-editor' ); ?></button>
                <button v-show="view=='selected'" class="e-button" type="button"
                        @click="_clear"
                        :disabled="!selectedItems.length"><?php esc_html_e( 'Clear', 'learnpress-frontend-editor' ); ?></button>
                <button v-show="view=='listing'" class="e-button" type="button"
                        :disabled="!selectedItems.length"
                        @click="_switch($event, 'selected')"><?php esc_html_e( 'Selected', 'learnpress-frontend-editor' ); ?>
                    {{countSelectedItems()}}
                </button>
                <button class="e-button close" type="button"
                        @click="_close"><?php esc_html_e( 'Cancel', 'learnpress-frontend-editor' ); ?></button>

                <e-modal-pagination v-if="pagination.total" :total="pagination.total" :page="pagination.current"
                                    @pagination-update="_changePage"></e-modal-pagination>
            </div>
        </div>
    </div>
</script>

<script type="text/x-template" id="tmpl-e-modal-pagination">
    <div class="e-pagination">
        <button class="e-button"
                @click="_reload"><?php esc_html_e( 'Reload', 'learnpress-frontend-editor' ); ?></button>
        <button type="button" class="e-button first" :disabled="page == 1"
                v-if="total > 2 && page > 1 && page != 2"
                @click.prevent="previousFirstPage">«
        </button>
        <button type="button" class="e-button button previous" :disabled="page == 1"
                v-if="total > 1"
                @click.prevent="previousPage"><?php echo esc_html_x( 'Previous', 'page-navigation', 'learnpress' ); ?></button>
        <button type="button" class="e-button button next" :disabled="page == total"
                v-if="total > 1"
                @click.prevent="nextPage"><?php echo esc_html_x( 'Next', 'page-navigation', 'learnpress' ); ?></button>
        <button type="button" class="e-button button last" :disabled="page == total"
                v-if="total > 2 && page < total && page != (total - 1)"
                @click.prevent="nextLastPage">»
        </button>
        <span class="index">{{page}} / {{total}}</span>

    </div>
</script>
