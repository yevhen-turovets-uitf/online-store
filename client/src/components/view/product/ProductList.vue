<template>
    <div>
        <h1>ProductList</h1>
        <div class="container">
            <button @click="openBasket" class="glyphicon glyphicon-shopping-cart" v-text="cartText">
                Open basket
            </button>
            <section style="background-color: #eee;">
                <div class="text-center container py-5 d-flex align-content">
                    <div
                        class="col-md-8 col-lg-6 col-xl-4 mb-5"
                        v-for="(product, index) in products"
                        :key="index"
                    >
                        <div class="card" style="border-radius: 15px;">
                            <div
                                class="bg-image hover-overlay ripple ripple-surface ripple-surface-light"
                                data-mdb-ripple-color="light"
                                @click="openDetail(product)"
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
                            <div class="info-wrap" v-if="product.variants && Object.keys(product.variants).length > 0">
                                <div class="rating text-center">
                                    <label>
                                        Base
                                        <input
                                            type="radio"
                                            :name="product.slug"
                                            value="0"
                                            :checked="product.variant.id === undefined"
                                            @change="ChooseSize(0, product)"
                                        >
                                    </label>
                                    <label v-for="variant in product.variants" :key="variant.id" class="ml-4">
                                        {{ variant.size.name }}
                                        <input
                                            type="radio"
                                            name="product.slug"
                                            :value="variant.id"
                                            :checked="variant.id === product.variant.id"
                                            @change="ChooseSize(variant, product)"
                                        >
                                    </label>
                                </div>
                            </div>
                            <hr class="my-0">
                            <div class="card-body pb-0" @click="openDetail(product)">
                                <div class="d-flex justify-content-between">
                                    <p class="text-danger" />
                                    <div :class="product.discountPrice > 0 ? 'text-danger' : 'text-dark'">
                                        $ {{ product.discountPrice > 0 ? product.discountPrice : product.price }}
                                        <strike v-if="product.discountPrice > 0" class="text-dark">
                                            <span>$ {{ product.price }}</span>
                                        </strike>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-1">
                                    <div>
                                        <p class="in-store">
                                            In store: {{ product.quantity }} items
                                        </p>
                                        <div class="qty">
                                            <span
                                                class="minus bg-light"
                                                :class="quantities[product.id] === 1 ? 'disabled' : ''"
                                                @click="changeQuantity(product.id, '-')"
                                            >-</span>
                                            <input
                                                type="number"
                                                min="1"
                                                :max="product.quantity"
                                                class="count"
                                                v-model="quantities[product.id]"
                                                @change="setLimits(product.id)"
                                                :disabled="product.quantity === 0"
                                            >
                                            <span
                                                class="plus bg-light"
                                                @click="changeQuantity(product.id, '+')"
                                                :class="quantities[product.id] === product.quantity || product.quantity === 0 ? 'disabled' : ''"
                                            >+</span>
                                        </div>
                                    </div>
                                    <button
                                        type="button" 
                                        :disabled="product.quantity === 0" 
                                        @click="onAddProduct(product)" 
                                        class="btn btn-success"
                                    >
                                        Buy now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <Detail ref="detail" />
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Detail from '@/components/view/product/Detail.vue';

export default {
    name: 'ProductList',

    data: () => ({
        quantities: [],
    }),

    components: {
        Detail
    },
  
    async created() {
        try {
            await this.getProducts();
            // eslint-disable-next-line no-return-assign
            Object.keys(this.products).forEach(id => this.quantities[id] = 1);
            // eslint-disable-next-line no-empty
        } catch (error) {}
    },

    computed: {
        ...mapGetters('product', {
            products: 'getAllProducts',
        }),
        ...mapGetters('cart', {
            cartCount: 'cartItemsCount',
        }),
      
        cartText() {
            return this.cartCount > 0 ? `Cart (${this.cartCount})` : 'Your cart is empty';
        }
    },

    methods: {
        ...mapActions('product', [
            'getProducts',
            'setProductVariant',
        ]),
        ...mapActions('cart', [
            'addProduct',
            'openCart',
        ]),
        onAddProduct(item) {
            const count = this.quantities[item.id];
            this.addProduct({ item, count });
        },
        openDetail(product) {
            this.$refs.detail.showDetail(product);
        },
        openBasket() {
            this.openCart();
        },
        changeQuantity(id, type) {
            const max = this.products[id].quantity;
            // eslint-disable-next-line no-mixed-operators
            if (this.quantities[id] !== max && type === '+' || this.quantities[id] !== 1 && type === '-') {
                if (max !== 0) {
                    this.quantities[id] += type === '+' ? 1 : -1;
                }
            }
            this.$set(this.quantities, id, this.quantities[id]);
        },
        setLimits(id) {
            if (this.quantities[id] > this.products[id].quantity) {
                this.quantities[id] = this.products[id].quantity;
            } else if (this.quantities[id] < 1) {
                this.quantities[id] = 1;
            }
            this.$set(this.quantities, id, this.quantities[id]);
        },
        ChooseSize(variant, product) {
            this.setProductVariant({ prodId: product.id, variantId: variant.id ? variant.id : 0 });
        },
    },
};
</script>

<style>
@import url('https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css');
.align-content {
  flex-wrap: wrap;
}
section {
  width: inherit;
}
.glyphicon-shopping-cart {
  margin-top: -30px;
  display: inline-flex;
  position: relative;
  float: right;
}
.in-store {
  font-size: x-small;
  margin-bottom: inherit;
}
.qty .count {
  color: #000;
  display: inline-block;
  vertical-align: top;
  line-height: 30px;
  padding: 0 2px;
  min-width: 35px;
  text-align: center;
}
.qty .plus {
  cursor: pointer;
  display: inline-block;
  vertical-align: top;
  color: black;
  width: 30px;
  height: 30px;
  font: 30px/1 Arial,sans-serif;
  text-align: center;
  border-radius: 50%;
}
.qty .minus {
  cursor: pointer;
  display: inline-block;
  vertical-align: top;
  color: black;
  width: 30px;
  height: 30px;
  font: 30px/1 Arial,sans-serif;
  text-align: center;
  border-radius: 50%;
  background-clip: padding-box;
}
div {
  text-align: center;
}
.minus:hover{
  background-color: #717fe0 !important;
}
.plus:hover{
  background-color: #717fe0 !important;
}
/*Prevent text selection*/
span{
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}
input{
  border: 0;
  width: 2%;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.disabled{
  color: lightgray!important;
}
.disabled:hover{
  background-color: #f8f9fa!important
}
input[type="radio"] {
  width: auto;
}
</style>
