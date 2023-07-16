import ApiRequestService from '@/services/ApiRequestService';
import { productMapper } from '@/services/Normalizer';
import {
    SET_PRODUCT_VARIANTS,
    SET_PRODUCT_VARIANT,
    SET_PRODUCTS,
} from './mutationTypes';

export default {
    async getProducts({ commit }) {
        try {
            const products = await ApiRequestService.get('/products');

            commit(SET_PRODUCTS, products);

            return Promise.resolve(
                products.map(productMapper)
            );
        } catch (error) {
            return Promise.reject(error);
        }
    },

    async getProductVariants({ commit }, id) {
        try {
            const variants = await ApiRequestService.get(`/products/${id}/variants`);

            commit(SET_PRODUCT_VARIANTS, { variants, id });

            return Promise.resolve();
        } catch (error) {
            return Promise.reject(error);
        }
    },

    setProductVariant({ commit }, { prodId, variantId }) {
        commit(SET_PRODUCT_VARIANT, { prodId, variantId });
    },
};
