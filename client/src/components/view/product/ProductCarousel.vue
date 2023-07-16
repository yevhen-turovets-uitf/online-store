<template>
    <div>
        <h1>ProductCarousel</h1>
        <div class="container">
            <section style="background-color: #eee;">
                <carousel class="text-center container py-5 d-flex align-content" :per-page="3">
                    <slide class="col-md-8 col-lg-6 col-xl-4 mb-5" v-for="(product, index) in products" :key="index">
                        <div class="card" style="border-radius: 15px;">
                            <div
                                class="bg-image hover-overlay ripple ripple-surface ripple-surface-light"
                                data-mdb-ripple-color="light"
                            >
                                <img
                                    :src="product.picture"
                                    style="border-top-left-radius: 15px; border-top-right-radius: 15px;"
                                    class="img-fluid"
                                >
                            </div>
                            <div class="card-body pb-0" @click="openDetail(product)">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-dark">{{ product.title }}</p>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-0">

                            <div class="card-body pb-0" @click="openDetail(product)">
                                <div class="d-flex justify-content-between">
                                    <p class="text-danger">
                                        <strike v-if="product.discountPrice" class="mb-3">
                                            $ {{ product.discountPrice }}
                                        </strike>
                                    </p>
                                    <p class="text-dark">$ {{ product.price }}</p>
                                </div>
                            </div>
                            <hr class="my-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-1">
                                    <input
                                        :ref="'quantity' + product.id"
                                        type="number"
                                        min="1"
                                        value="1"
                                        :max="product.quantity"
                                    >
                                    <button type="button" @click="onAddProduct(product)" class="btn btn-success">Buy now</button>
                                </div>
                            </div>
                        </div>
                    </slide>
                </carousel>
            </section>
        </div>
        <Detail ref="detail" />
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { Carousel, Slide } from 'vue-carousel';
import Detail from '@/components/view/product/Detail.vue';

export default {
    name: 'ProductCarousel',

    components: {
        Carousel,
        Slide,
        Detail
    },

    computed: {
        ...mapGetters('product', {
            products: 'getAllProducts',
        }),
    },

    methods: {
        ...mapActions('product', [
            'getProducts',
        ]),
        ...mapActions('cart', [
            'addProduct',
        ]),
        onAddProduct(item) {
            const count = this.$refs[`quantity${item.id}`][0].value;
            this.addProduct({ item, count });
        },
        openDetail(product) {
            this.$refs.detail.showDetail(product);
        },
    },
  
    async created() {
        try {
            await this.getProducts();
        } catch (error) {
            // eslint-disable-next-line no-console
            console.log(error);
        }
    },
};
</script>

<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
