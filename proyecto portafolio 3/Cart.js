import React, { useContext } from "react";
import { CartContext } from "./GlobalState";

export const Cart = () => {
  const { state, dispatch } = useContext(CartContext);

  const total = state.cart.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0
  );

  return (
    <div>
      <h2>Carrito</h2>
      {state.cart.length === 0 ? (
        <p>El carrito está vacío</p>
      ) : (
        <ul>
          {state.cart.map(item => (
            <li key={item.id}>
              {item.name} x {item.quantity} = ${item.price * item.quantity}{" "}
              <button
                onClick={() =>
                  dispatch({ type: "REMOVE_FROM_CART", payload: { id: item.id } })
                }
              >
                Eliminar
              </button>
            </li>
          ))}
        </ul>
      )}
      <h3>Total: ${total}</h3>
    </div>
  );
};
