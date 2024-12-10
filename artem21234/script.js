document.addEventListener('DOMContentLoaded', () => {
    // Переключение между режимами авторизации
    const toggleLink = document.getElementById('toggle-link');
    const loginBtn = document.getElementById('login-btn');
    const registerBtn = document.getElementById('register-btn');
    const toggleMsg = document.getElementById('toggle-msg');
    const submitBtn = document.querySelector('.submit-btn');
    const authForm = document.getElementById('auth-form');

    toggleLink.addEventListener('click', function (e) {
        e.preventDefault();
        if (loginBtn.classList.contains('active')) {
            setRegisterMode();
        } else {
            setLoginMode();
        }
    });

    loginBtn.addEventListener('click', setLoginMode);
    registerBtn.addEventListener('click', setRegisterMode);

    function setLoginMode() {
        const form = document.getElementById('auth-form');
        form.reset();
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
        submitBtn.textContent = 'Войти';
        toggleMsg.innerHTML = 'Нет аккаунта? <a href="#" id="toggle-link">Зарегистрироваться</a>';
    }

    function setRegisterMode() {
        const form = document.getElementById('auth-form');
        form.reset();
        loginBtn.classList.remove('active');
        registerBtn.classList.add('active');
        submitBtn.textContent = 'Зарегистрироваться';
        toggleMsg.innerHTML = 'Уже есть аккаунт? <a href="#" id="toggle-link">Войти</a>';
    }

    // Обработчик отправки формы авторизации
    authForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const action = loginBtn.classList.contains("active") ? "login" : "register";
        formData.append("action", action);

        try {
            const response = await fetch("auth.php", {
                method: "POST",
                body: formData,
            });
            const result = await response.text();
            alert(result);
        } catch (error) {
            console.error('Ошибка:', error);
        }
    });

    // Корзина
    const addToCartBtn = document.getElementById('add-to-cart');
    const cartItemsList = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');

    // Хранилище корзины
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Функция обновления корзины
    function updateCart() {
        if (cartItemsList) {
            cartItemsList.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.textContent = `${item.name} - ${item.price} ₽`;

                // Кнопка для удаления товара
                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'Удалить';
                removeBtn.addEventListener('click', () => {
                    cart.splice(index, 1);
                    saveCart();
                    updateCart();
                });

                li.appendChild(removeBtn);
                cartItemsList.appendChild(li);
                total += item.price;
            });

            cartTotal.textContent = `Итого: ${total} ₽`;
        }
    }

    // Функция сохранения корзины
    function saveCart() {
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    // Обработчик добавления товара в корзину
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            const productName = addToCartBtn.dataset.name;
            const productPrice = parseInt(addToCartBtn.dataset.price);

            cart.push({ name: productName, price: productPrice });
            saveCart();
            alert(`Товар "${productName}" добавлен в корзину!`);
            updateCart(); // Обновляем корзину после добавления
        });
    }

    // Переход к оформлению заказа
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            if (cart.length === 0) {
                alert('Ваша корзина пуста.');
                return;
            }
            alert('Спасибо за покупку!');
            cart = [];
            saveCart();
            updateCart();
        });
    }

    // Инициализация корзины при загрузке
    updateCart();
});

// Функция сброса фильтров
function resetFilter() {
    document.getElementById("category").value = "all";
    document.getElementById("stock").value = "all";
    document.getElementById("rating").value = "0";
    document.getElementById("min-price").value = "";
    document.getElementById("max-price").value = "";

    const products = document.querySelectorAll('.product-card');
    products.forEach(product => {
        product.style.display = 'block';
    });
}
