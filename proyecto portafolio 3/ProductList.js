import React, { useContext } from "react";
import { CartContext } from "./GlobalState";

export const ProductList = () => {
  const { state, dispatch } = useContext(CartContext);

  return (
    <div>
      <h2>Productos</h2>
      <ul>
        {state.products.map(product => (
          <li key={product.id}>
            {product.name} - ${product.price}{" "}
            <button
              onClick={() => dispatch({ type: "ADD_TO_CART", payload: product })}
            >
              Añadir al carrito
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
};
