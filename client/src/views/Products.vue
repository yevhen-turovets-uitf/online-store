<template>
    <div class="container desktop">
        <ProductList />
        <ProductCarousel />
        <Cart />
    </div>
</template>

<script>
import ProductList from '@/components/view/product/ProductList.vue';
import ProductCarousel from '@/components/view/product/ProductCarousel.vue';
import Cart from '@/components/view/cart/Cart.vue';

export default {
    name: 'ProductsList',

    components: {
        ProductList,
        ProductCarousel,
        Cart,
    },
};
</script>

<style lang="scss" scoped>
</style>
