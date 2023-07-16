class Storage {
    constructor() {
        this.cartName = 'cart';
        this.store = window.localStorage;
    }

    get(key) {
        return JSON.parse(this.store.getItem(key));
    }

    set(key, value) {
        return this.store.setItem(key, JSON.stringify(value));
    }

    getCart() {
        return this.get(this.cartName);
    }

    setCart(cart) {
        return this.set(this.cartName, cart);
    }

    hasCart() {
        return !!this.get(this.cartName);
    }
}

export default new Storage();
