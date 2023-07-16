export default {
    getCartItems: (state) => state.items,
    showCart: (state) => state.showCart,
    cartItemsCount: (state) => Object.keys(state.items).length,
};
