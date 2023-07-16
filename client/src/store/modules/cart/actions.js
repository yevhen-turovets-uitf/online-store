import {
    ADD_PRODUCT,
    DELETE_PRODUCT,
    SHOW_CART,
    SET_COUNT_PRODUCT,
} from './mutationTypes';

export default {
    addProduct({ commit }, { item, count }) {
        commit(ADD_PRODUCT, { item, count });
        commit(SHOW_CART);
    },

    deleteProduct({ commit }, { id }) {
        commit(DELETE_PRODUCT, id);
    },

    setCount({ commit }, item) {
        commit(SET_COUNT_PRODUCT, { item });
    },

    openCart({ commit }) {
        commit(SHOW_CART);
    },
};
