import React from "react";
import { CartProvider } from "./GlobalState";
import { ProductList } from "./ProductList";
import { Cart } from "./Cart";

function App() {
  return (
    <CartProvider>
      <h1>Mi Tienda</h1>
      <ProductList />
      <Cart />
    </CartProvider>
  );
}

export default App;
