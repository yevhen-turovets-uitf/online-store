import { productMapper } from '@/services/Normalizer';
import {
    SET_PRODUCTS,
    SET_PRODUCT_VARIANTS,
    SET_PRODUCT_VARIANT,
} from './mutationTypes';

export default {
    [SET_PRODUCTS]: (state, products) => {
        state.products = {
            ...state.products,
            ...products.reduce(
                (prev, product) => ({ ...prev, [product.id]: productMapper(product) }),
                {}
            ),
        };
    },

    [SET_PRODUCT_VARIANTS]: (state, data) => {
        if (state.products[data.id]) {
            state.products[data.id].variants = data.variants;
            if (!state.products[data.id].variant) {
                state.products[data.id].variant = {};
                state.products[data.id].oldPrice = state.products[data.id].price;
                state.products[data.id].oldDscPrice = state.products[data.id].discountPrice;
                state.products[data.id].oldQuantity = state.products[data.id].quantity;
            }
        }
    },

    [SET_PRODUCT_VARIANT]: (state, data) => {
        if (state.products[data.prodId]) {
            state.products[data.prodId].variant = data.variantId;
            // eslint-disable-next-line no-shadow,max-len
            const key = Object.keys(state.products[data.prodId].variants).find(key => state.products[data.prodId].variants[key].id === data.variantId);
            if (key) {
                state.products[data.prodId].price = state.products[data.prodId].variants[key].price;
                state.products[data.prodId].discountPrice = state.products[data.prodId].variants[key].discountPrice;
                state.products[data.prodId].quantity = state.products[data.prodId].variants[key].qty;
                state.products[data.prodId].variant = state.products[data.prodId].variants[key];
            } else {
                state.products[data.prodId].price = state.products[data.prodId].oldPrice;
                state.products[data.prodId].discountPrice = state.products[data.prodId].oldDscPrice;
                state.products[data.prodId].quantity = state.products[data.prodId].oldQuantity;
                state.products[data.prodId].variant = {};
            }
        }
    },
};
