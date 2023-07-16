import Storage from '@/services/Storage';
import Vue from 'vue';

import {
    ADD_PRODUCT,
    DELETE_PRODUCT,
    SHOW_CART,
    SET_COUNT_PRODUCT,
} from './mutationTypes';

export default {
    [ADD_PRODUCT]: (state, data) => {
        if (state.items[data.item.id]) {
            data.count += state.items[data.item.id].added;
            if (data.count > state.items[data.item.id].quantity) {
                data.count = state.items[data.item.id].quantity;
            }
        }
        state.items = {
            ...state.items,
            [data.item.id]: data.item
        };
        state.items[data.item.id].added = data.count;
        Storage.setCart(state.items);
    },

    [DELETE_PRODUCT]: (state, id) => {
        if (!state.items[id]) {
            return;
        }
        Vue.delete(state.items, id);
        Storage.setCart(state.items);
    },

    [SHOW_CART]: (state) => {
        state.showCart = !state.showCart;
    },

    [SET_COUNT_PRODUCT]: (state, data) => {
        state.items[data.item.id].added = data.item.added;
        Storage.setCart(state.items);
    },
};
