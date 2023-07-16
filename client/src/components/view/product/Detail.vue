<template>
    <div
        class="modal fade show"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true" 
        v-show="open"
    >
        <div class="container d-flex justify-content-center" v-if="product">
            <figure class="card card-product-grid card-lg">
                <div class="bottom-wrap">
                    <div class="price-wrap">
                        <button class="btn btn-light float-right" @click="hideDetail"> X </button>
                    </div>
                </div>
              
                <img :src="product.picture">
                <figcaption class="info-wrap">
                    <div class="row">
                        <div class="col-md-8 col-xs-8">
                            <span class="title" v-text="product.title">Dell Xtreme 270</span>
                        </div>
                    </div>
                </figcaption>
              
                <div class="info-wrap" v-if="product.variants && Object.keys(product.variants).length > 0">
                    <div class="rating text-center">
                        <label>
                            Base
                            <input
                                type="radio"
                                name="product.slug"
                                value="0"
                                :checked="product.variant.id === undefined"
                                @change="ChooseSize(0)"
                            >
                        </label>
                        <label v-for="variant in product.variants" :key="variant.id" class="ml-5">
                            {{ variant.size.name }}
                            <input
                                type="radio"
                                name="product.slug"
                                :value="variant.id"
                                :checked="variant.id === product.variant.id"
                                @change="ChooseSize(variant)"
                            >
                        </label>
                    </div>
                </div>
              
                <div class="bottom-wrap-payment">
                    <figcaption class="info-wrap">
                        <div class="row">
                            <div class="ml-auto mr-3">
                                <div :class="product.discountPrice > 0 ? 'text-danger' : 'text-dark'">
                                    $ {{ product.discountPrice > 0 ? product.discountPrice : product.price }}
                                    <strike v-if="product.discountPrice > 0" class="text-dark">
                                        <span>$ {{ product.price }}</span>
                                    </strike>
                                </div>
                            </div>
                        </div>
                    </figcaption>
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
                                    @click="changeQuantity('-')"
                                    :class="quantity === 1 ? 'disabled' : ''"
                                >-</span>
                                <input
                                    type="number"
                                    min="1"
                                    v-model="quantity"
                                    :max="product.quantity"
                                    class="count"
                                    name="qty"
                                    @change="setLimits()"
                                    :disabled="product.quantity === 0"
                                >
                                <span
                                    class="plus bg-light" 
                                    :class="quantity === product.quantity || product.quantity === 0 ? 'disabled' : ''"
                                    @click="changeQuantity('+')"
                                    :disabled="product.quantity === 0"
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
            </figure>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'Detail',

    data: () => ({
        open: false,
        product: {},
        quantity: 1,
    }),

    computed: {
        ...mapGetters('product', {
            products: 'getAllProducts',
        }),
    },
  
    methods: {
        ...mapActions('product', [
            'getProductVariants',
            'setProductVariant',
        ]),
        ...mapActions('cart', [
            'addProduct',
        ]),
        async showDetail(product) {
            try {
                await this.getProductVariants(product.id);
                this.product = product;
                this.open = true;
            // eslint-disable-next-line no-empty
            } catch (error) {}
        },
        hideDetail() {
            this.open = false;
        },
        ChooseSize(variant) {
            this.setProductVariant({ prodId: this.product.id, variantId: variant.id ? variant.id : 0 });
        },
        onAddProduct(item) {
            this.addProduct({ item, count: this.quantity });
        },
        changeQuantity(type) {
            // eslint-disable-next-line no-mixed-operators
            if (this.quantity < this.product.quantity && type === '+' || this.quantity > 1 && type === '-') {
                if (this.product.quantity !== 0) {
                    this.quantity += type === '+' ? 1 : -1;
                }
            }
        },
        setLimits() {
            if (this.quantity > this.product.quantity) {
                this.quantity = this.product.quantity;
            } else if (this.quantity < 1) {
                this.quantity = 1;
            }
        }
    },
};
</script>

<style scoped>
body{
  background-color: #EEEEEE
}
a{
  text-decoration: none !important
}
.card-product-grid{
  margin-bottom: 0
}
.card{
  width: 500px;
  position: relative;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 23px;
  margin-top: 50px
}
.card-product-grid:hover{
  -webkit-box-shadow: 0 4px 15px rgba(153, 153, 153, 0.3);
  box-shadow: 0 4px 15px rgba(153, 153, 153, 0.3);
  -webkit-transition: .3s;
  transition: .3s
}
[class*='card-product'] .img-wrap img{
  height: 100%;
  max-width: 100%;
  width: auto;
  display: inline-block;
  -o-object-fit: cover;
  object-fit: cover
}
.card-product-grid .info-wrap{
  overflow: hidden;
  padding: 18px 20px
}
.rating-stars li{
  display: block;
  text-overflow: clip;
  white-space: nowrap;
  z-index: 1
}
.card-product-grid .bottom-wrap{
  padding: 18px;
  border-top: 1px solid #e4e4e4
}
.bottom-wrap-payment{
  padding: 0px;
  border-top: 1px solid #e4e4e4
}
.btn{
  display: inline-block;
  font-weight: 600;
  color: #343a40;
  text-align: center;
  vertical-align: middle;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  background-color: transparent;
  border: 1px solid transparent;
  padding: 0.45rem 0.85rem;
  font-size: 1rem;
  line-height: 1.5;
  border-radius: 0.2rem
}
.show {
  display: block;
  position: fixed;
  margin-left: auto;
  margin-right: auto;
  background: rgba(255, 255, 522, 0.7);
}
.btn-success {
  color: #fff;
  background-color: #28a745;
  border-color: #28a745;
}
</style>
