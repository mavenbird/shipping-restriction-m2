/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_Shiprestriction
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/ui-select',
    'jquery'
], function (_, select, $) {
    'use strict';

    return select.extend({
        toggleOptionSelected: function (data) {
            this._super(data);

            if (data.hasOwnProperty(this.separator)) {
                // Hide and unselect all nested levels
                var self = this;
                if (this.isSelected(data.value)) {
                    if (data.visible) {
                        data.visible(false);
                    }
                    _.each(data[this.separator], function (child) {
                        self.unSelectChilds(child);
                    });
                }
            } else {
                var parent = this.getPrent(data);
                if (this.isSelected(data.value)) {
                    if (this.isSelected(parent.value)) {
                        //unselect parent
                        this.value.remove(parent.value);
                    }
                    if (this.isSelectedAllChilds(parent)) {
                        //if all childs selected then unselect and hide them, select parent
                        this.toggleOptionSelected(parent);
                    }
                }
            }

            return this;
        },

        getPrent: function (child) {
            return _.find(this.options(), function (option) {
                return option['label'] === child.path;
            }, this);
        },

        isSelectedAllChilds: function (parent) {
            var selectedAll = true;
            _.some(parent[this.separator], function (element) {
                if (!this.isSelected(element.value)) {
                    selectedAll = false;
                    return false;
                }
            }.bind(this));

            return selectedAll;
        },

        unSelectChilds: function (data) {
            if (this.isSelected(data.value)) {
                this.value.remove(data.value)
            }
            if (data.visible) {
                data.visible(false)
            }
            if (data.hasOwnProperty(this.separator)) {
                var self = this;
                _.each(data[this.separator], function (child) {
                    self.unSelectChilds(child);
                });
            }
        }
    });
});
