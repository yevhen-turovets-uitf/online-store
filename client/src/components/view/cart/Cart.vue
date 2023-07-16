<template>
    <div
        v-if="show"
        class="modal fade show"
        id="cartModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Your Shopping Cart
                    </h5>
                    <button
                        @click="toggleShow()"
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-image">
                        <thead>
                            <tr>
                                <th scope="col" />
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Total</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="items">
                            <tr v-for="item in items" :key="item.id">
                                <td class="w-25">
                                    <img
                                        :src="item.picture"
                                        class="img-fluid img-thumbnail"
                                        :alt="item.title"
                                    >
                                </td>
                                <td>
                                    {{ item.title }}
                                    <p v-if="hasVariants(item)">SIZE: {{ item.variant.size.name }}</p>
                                </td>
                                <td>
                                    <div :class="item.discountPrice > 0 ? 'text-danger' : 'text-dark'">
                                        <strike v-if="item.discountPrice > 0" class="text-dark">
                                            <p class="mb-0">$ {{ item.price }}</p>
                                        </strike>
                                        ${{ item.discountPrice > 0 ? item.discountPrice : item.price }}
                                    </div>
                                </td>
                                <td class="qty">
                                    <span
                                        class="minus bg-light"
                                        :class="item.added === 1 ? 'disabled' : ''"
                                        @click="changeQuantity(item, '-')"
                                    >-</span>
                                    <input
                                        type="number"
                                        :max="item.quantity"
                                        min="1"
                                        class="count"
                                        v-model="item.added"
                                        @change="onChangeCount(item)"
                                    >
                                    <span
                                        class="plus bg-light"
                                        @click="changeQuantity(item, '+')"
                                        :class="item.added === item.quantity ? 'disabled' : ''"
                                    >+</span>
                                    <p class="in-store">
                                        In store: {{ item.quantity }} items
                                    </p>
                                </td>
                                <td>${{ sums[item.id] }}</td>
                                <td>
                                    <button @click="onDelete(item.id)" class="btn btn-danger btn-sm">
                                        <i class="fa fa-times">X</i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                        <h5>Total: <span class="price text-success">$ {{ total }}</span></h5>
                    </div>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-between">
                    <button
                        type="button"
                        @click="toggleShow()"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                    <button
                        type="button"
                        @click="toggleShow()"
                        :disabled="empty"
                        class="btn btn-success"
                    >
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex';

export default {
    name: 'Cart',

    data: () => ({
        total: 0,
        sums: [],
        empty: false,
    }),

    computed: {
        ...mapGetters('cart', {
            show: 'showCart',
            items: 'getCartItems',
        }),
    },

    methods: {
        ...mapMutations('cart', [
            'showCart',
        ]),
        ...mapActions('cart', [
            'addProduct',
            'deleteProduct',
            'setCount',
        ]),
        toggleShow() {
            this.showCart();
        },
        onDelete(id) {
            this.deleteProduct({ id });
            this.updateTotal();
        },
        onChangeCount(item) {
            // eslint-disable-next-line no-nested-ternary
            item.added = item.added > item.quantity ? item.quantity : (item.added < 1) ? 1 : item.added;

            this.setCount(item);
            this.updateTotal();
        },
        updateTotal() {
            let sum = 0;
            Object.values(this.items).forEach((item) => {
                const price = item.discountPrice > 0 ? item.discountPrice : item.price;
                // eslint-disable-next-line no-multi-assign
                sum += this.sums[item.id] = item.added * price;
            });
            this.total = sum;
            this.isEmpty();
        },
        isEmpty() {
            this.empty = Object.keys(this.items).length === 0;
        },
        hasVariants(item) {
            return item.variant && Object.keys(item.variant).length > 0;
        },
        changeQuantity(item, type) {
            const max = item.quantity;
            // eslint-disable-next-line no-mixed-operators
            if (item.added !== max && type === '+' || item.added !== 1 && type === '-') {
                item.added += type === '+' ? 1 : -1;
            }
            this.onChangeCount(item);
        },
    },
    watch: {
        '$store.state.cart.showCart': function (open) {
            if (open) {
                this.updateTotal();
            }
        }
    }
};
</script>

<style scoped>
.container {
  padding: 2rem 0rem;
}
.table-image {
    thead {
    td, th {
      border: 0;
      color: #666;
      font-size: 0.8rem;
    }
    }
    td, th {
      vertical-align: middle;
      text-align: center;

    &.qty {
       max-width: 2rem;
     }
    }
}
.price {
  margin-left: 1rem;
}

.modal-footer {
  padding-top: 0rem;
}
.show {
  opacity: 1!important;
  display: block!important;
  background: rgba(255, 255, 522, 0.7);
  scroll-behavior: auto;
  overflow: scroll;
}
.qty-input {
  max-width: 70px;
}
</style>
