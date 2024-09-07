const body = document.querySelector("body");
const shoppingBasket = document.querySelector(".shoppingBasket");
const closeCart = document.querySelector(".close");
const productList = document.querySelector(".productList");
const quantity = document.querySelector(".quantity");
const total = document.querySelector(".total");
const drinkBtn = document.getElementById("drinks");
const dessertBtn = document.getElementById("desserts");

let checkOutList = [];
let menuItemsCache = []; // Initialize cache

// Cart button activation
shoppingBasket.onclick = () => {
  body.classList.add("active");
};

// Close cart
closeCart.onclick = () => {
  body.classList.remove("active");
};

// Fetch menu items from the backend
async function getMenuItems() {
  try {
    // Add a timestamp to the query string to prevent caching
    const response = await fetch(`../actions/get_menu_items.php?t=${new Date().getTime()}`);
    const menuItems = await response.json();
    console.log("Menu Items Fetched:", menuItems);

    // Update cache
    menuItemsCache = menuItems;
    return menuItems;
  } catch (error) {
    console.error("Error fetching menu items:", error);
    return [];
  }
}

// Render items to the UI
function renderItems(menuItems) {
  const products = document.querySelector(".products");
  products.innerHTML = ''; // Clear current items

  menuItems.forEach(item => {
    let div = document.createElement("div");
    div.classList.add("item");

    div.innerHTML = `
      <img src="../public/assets/images/${item.photo_url}" alt="${item.name}" style="height: 200px; object-fit: cover;">
      <div class="name">${item.name}</div>
      <div class="prices" style="display:inline">${item.price}</div>
      <div class="prices" style="display:inline">MMK</div>
      <button class="btn_addToCart" style="display:block; margin-left:60px" onclick="addToCart('${item.id}')">Add to Cart</button>
    `;

    products.appendChild(div);
  });
}

// Fetch items and display them based on category
async function showItems(category) {
  console.log(`Fetching items for category: ${category}`);
  const menuItems = await getMenuItems();
  const filteredItems = menuItems.filter(item => item.category === category);
  renderItems(filteredItems);
}

// Event listeners for category buttons
drinkBtn.onclick = () => showItems("drinks");
dessertBtn.onclick = () => showItems("desserts");

// Initialize with 'drinks' category
showItems("drinks");

// Add items to cart
function addToCart(id) {
  console.log("Add to Cart clicked for ID:", id); // Debugging line

  // Ensure ID is treated as a string for comparison
  const item = menuItemsCache.find(item => String(item.id) === String(id)); 
  console.log("Found item:", item); // Debugging line

  if (item) {
    const existingItemIndex = checkOutList.findIndex(cartItem => String(cartItem.id) === String(id));

    if (existingItemIndex === -1) {
      checkOutList.push({ ...item, quantity: 1 });
    } else {
      checkOutList[existingItemIndex].quantity += 1;
    }

    reloadCart();
    
    // Show the cart when an item is added
    body.classList.add("active");
  } else {
    console.error("Item not found:", id); // Log the ID if item is not found
  }
}

// Reload cart with updated items
function reloadCart() {
  productList.innerHTML = "";
  let count = 0;
  let totalPrice = 0;

  checkOutList.forEach((item, key) => {
    totalPrice += parseFloat(item.price * item.quantity);
    count += item.quantity;

    let li = document.createElement("li");
    li.innerHTML = `
      <img src="../public/assets/images/${item.photo_url}" style="height: 50px;"/>
      <div class="name" style="padding-left:10px;">${item.name}</div>
      <div class="count">${item.price}</div>
      <div>
        <button onclick="changeQuantity(${key}, ${item.quantity - 1})"> - </button>
        <div class="count">${item.quantity}</div>
        <button onclick="changeQuantity(${key}, ${item.quantity + 1})"> + </button>
      </div>
    `;

    productList.appendChild(li);
  });

  total.innerHTML = `<small>Subtotal (${count} items) $</small>` + totalPrice.toFixed(2);
  quantity.innerHTML = count;
}

// Change quantity of items in cart
function changeQuantity(key, quantity) {
  if (quantity <= 0) {
    checkOutList.splice(key, 1);
  } else {
    checkOutList[key].quantity = quantity;
  }
  reloadCart();
}

// Prepare order data
function prepareOrderData() {
  const formData = new FormData();

  // Ensure customer name is properly captured
  const customerName = document.querySelector('#customer_name') ? document.querySelector('#customer_name').value : '';
  formData.append('customer_name', customerName);

  // Append menu items and quantities
  const menuItems = [];
  const quantities = {};
  checkOutList.forEach(item => {
    menuItems.push(item.id);
    quantities[item.id] = item.quantity;
  });

  formData.append('menu_items', JSON.stringify(menuItems));
  formData.append('quantities', JSON.stringify(quantities));

  return formData;
}

async function submitOrder(event) {
  event.preventDefault();

  const formData = prepareOrderData();

  try {
    const response = await fetch('../actions/submit_order.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.text();

    if (response.ok) {
      window.location.href = '../public/thankyou.php';
    } else {
      console.error("Error submitting order:", result);
    }
  } catch (error) {
    console.error("Network error:", error);
  }
}

document.querySelector('.submit').addEventListener('click', submitOrder);