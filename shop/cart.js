export function cart_manager() {
    let cart = localStorage.getItem('cart');

    if (!cart) {
        localStorage.setItem('cart', JSON.stringify({}));
    }
}


export function add_particular_item(id) {
    let cart = JSON.parse(localStorage.getItem('cart')) || {};

    if (!cart[id]) {
        cart[id] = { count: 0 };
    }

    cart[id].count += 1;

    localStorage.setItem('cart', JSON.stringify(cart));
}

export function remove_particular_item(){
    let wholeObject = JSON.parse(localStorage.getItem('cart'));

    if (!cart[id]) {
        cart[id] = { count: 0 };
    }

    cart[id].count -= 1;

    localStorage.setItem('cart', JSON.stringify(cart));
}

export function items_checkout(){
    return JSON.parse(localStorage.getItem('cart'));
}