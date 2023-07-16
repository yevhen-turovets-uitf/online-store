import Storage from '@/services/Storage';

export default {
    items: Storage.hasCart() ? Storage.getCart() : {},
    showCart: false,
};
